<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\ExamResponseResource;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResponse;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    use APIResponse;

    public function createExam(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'forwardable_type' => 'required|string|in:User,Group,Batch',
            'forwardable_id' => 'required|integer',
            'start_time' => 'nullable|date_format:Y-m-d H:i:s',
            'end_time' => 'nullable|date_format:Y-m-d H:i:s|after:start_time',
        ]);

        $model = "App\\Models\\" . $request->forwardable_type;
        if (!class_exists($model)) {
            return $this->errorResponse("نوع العنصر غير متاح.", 422);
        }

        $forward = $model::find($request->forwardable_id);
        if (!$forward) {
            return $this->errorResponse("العنصر غير موجود.", 404);
        }

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'admin_id' => Auth::id(),
            'forwardable_type' => $model,
            'forwardable_id' => $request->forwardable_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return $this->successResponse(new ExamResource($exam), 'تم إنشاء الإختبار بنجاح');
    }

    public function addQuestions(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'questions' => 'required|array',
            'questions.*.type' => 'required|in:string,text,multiple_choice,checkbox',
            'questions.*.question' => 'required|string',
            'questions.*.is_required' => 'boolean',
            'questions.*.grade' => 'integer|min:1',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.is_correct' => 'boolean',
        ]);

        $exam = Exam::find($request->exam_id);

        $questions = [];
        foreach ($request->questions as $questionData) {
            $question = $exam->questions()->create([
                'type' => $questionData['type'],
                'question' => $questionData['question'],
                'is_required' => $questionData['is_required'] ?? false,
                'grade' => $questionData['grade'] ?? 1,
            ]);

            if (isset($questionData['options']) && in_array($questionData['type'], ['multiple_choice', 'checkbox'])) {
                $question->options()->createMany($questionData['options']);
            }
            $questions[] = $question;
        }
        $question->load('options');

        return $this->successResponse(QuestionResource::collection($questions), 'تم إضافة السؤال بنجاح');
    }

    public function deleteQuestion($id)
    {
        $question = Question::find($id);
        if (!$question) {
            return $this->errorResponse('السؤال غير موجود', 404);
        }

        $question->options()->delete();
        $question->delete();

        return $this->successResponse([], 'تم حذف السؤال بنجاح');
    }

    public function getExams()
    {
        $exams = Exam::all();
        return $this->successResponse(ExamResource::collection($exams), 'Exams retrieved successfully');
    }

    public function getExamDetails($id)
    {
        $exam = Exam::with('questions.options')->find($id);
        if (!$exam) {
            return $this->errorResponse('الإختبار غير موجود', 404);
        }

        return $this->successResponse(new ExamResource($exam), 'Exams retrieved successfully');
    }

    public function submitResponse(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|integer|exists:questions,id',
            'responses.*.response' => 'required',
        ]);

        $requiredQuestions = Question::where('exam_id', $request->exam_id)
            ->where('is_required', true)
            ->pluck('id')
            ->toArray();

        $responseQuestionIds = collect($request->responses)->pluck('question_id')->toArray();

        $missingQuestions = array_diff($requiredQuestions, $responseQuestionIds);

        if (!empty($missingQuestions)) {
            return response()->json([
                'message' => 'بعض الأسئلة غير مجاب عليها.',
                'missing_questions' => $missingQuestions,
            ], 400);
        }

        foreach ($request->responses as $responseData) {
            $question = Question::find($responseData['question_id']);

            if ($question) {
                $validationRules = [];

                if ($question->type === 'checkbox') {
                    $validationRules[] = 'array';
                } elseif ($question->type === 'multiple_choice') {
                    $validationRules[] = 'integer';
                } elseif (in_array($question->type, ['string', 'text'])) {
                    $validationRules[] = 'string';
                }

                $validator = Validator::make(
                    ['response' => $responseData['response']],
                    ['response' => $validationRules]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed for ' . $question,
                        'errors' => $validator->errors(),
                    ], 400);
                }
            }
        }

        foreach ($request->responses as $responseData) {
            $question = Question::find($responseData['question_id']);

            $existingResponses = ExamResponse::where('exam_id', $request->exam_id)
                ->where('user_id', Auth::id());
            // $existingResponse = $existingResponses->where('question_id', $responseData['question_id'])->first();
            if ($existingResponses->count() > 0) {
                return $this->errorResponse('تم إجابة هذا الإمتحان من قبل', ExamResponseResource::collection($existingResponses->get()), 500);
            }
        }

        $responses = [];
        foreach ($request->responses as $responseData) {
            $responses[] = ExamResponse::create([
                'exam_id' => $request->exam_id,
                'question_id' => $responseData['question_id'],
                'user_id' => Auth::id(),
                'response' => json_encode($responseData['response']),
            ]);
        }

        return $this->successResponse(ExamResponseResource::collection($responses), 'Responses submitted successfully');
    }

    public function viewResponses($examId)
    {
        $responses = ExamResponse::where('exam_id', $examId)->with('question')->get();
        return $this->successResponse(ExamResponseResource::collection($responses), 'Responses retrieved successfully');
    }
}
