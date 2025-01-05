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
            'is_apply' => 'nullable|boolean,default:false',
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
            'is_apply' => $request->is_apply,
        ]);

        return $this->successResponse(new ExamResource($exam), 'تم إنشاء الإختبار بنجاح');
    }

    public function editExam(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'forwardable_type' => 'sometimes|required|string|in:User,Group,Batch',
            'forwardable_id' => 'sometimes|required|integer',
            'start_time' => 'nullable|date_format:Y-m-d H:i:s',
            'end_time' => 'nullable|date_format:Y-m-d H:i:s|after:start_time',
            'is_apply' => 'nullable|boolean,default:false',
        ]);

        $exam = Exam::find($id);

        if (!$exam) {
            return $this->errorResponse("الاختبار غير موجود.", 404);
        }

        if ($request->has('forwardable_type') && $request->has('forwardable_id')) {
            $model = "App\\Models\\" . $request->forwardable_type;
            if (!class_exists($model)) {
                return $this->errorResponse("نوع العنصر غير متاح.", 422);
            }

            $forward = $model::find($request->forwardable_id);
            if (!$forward) {
                return $this->errorResponse("العنصر غير موجود.", 404);
            }

            $exam->forwardable_type = $model;
            $exam->forwardable_id = $request->forwardable_id;
        }

        $exam->update(
            $request->only(['title', 'description', 'start_time', 'end_time', 'is_apply'])
        );

        return $this->successResponse(new ExamResource($exam), 'تم تعديل الإختبار بنجاح');
    }


    public function addQuestions(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'questions' => 'required|array',
            'questions.*.id' => 'nullable|integer|exists:questions,id',
            'questions.*.type' => 'required|in:string,text,multiple_choice,checkbox',
            'questions.*.question' => 'required|string',
            'questions.*.is_required' => 'boolean',
            'questions.*.grade' => 'integer|min:1',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.id' => 'nullable|integer|exists:options,id',
            'questions.*.options.*.option_text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.is_correct' => 'boolean',
        ]);

        $exam = Exam::find($request->exam_id);

        $questions = [];
        foreach ($request->questions as $questionData) {
            $question = $exam->questions()->updateOrCreate(
                ['id' => $questionData['id'] ?? null],
                [
                    'id' => $questionData['id'] ?? null,
                    'type' => $questionData['type'],
                    'question' => $questionData['question'],
                    'is_required' => $questionData['is_required'] ?? false,
                    'grade' => $questionData['grade'] ?? 1,
                ],
            );

            if (isset($questionData['options']) && in_array($questionData['type'], ['multiple_choice', 'checkbox'])) {
                // $question->options()->createMany($questionData['options']);
                foreach ($questionData['options'] as $optionData) {
                    $question->options()->updateOrCreate(
                        ['id' => $optionData['id'] ?? null],
                        [
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                        ]
                    );
                }
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

    public function getExams(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 10) : 0;
        $searchQuery = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = Exam::query();

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%' . $searchQuery . '%');
            });
        }

        $query->orderBy($sortBy, $orderBy);

        $exams = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );
        return $this->successResponse(ExamResource::collection($exams)->response()->getData(), 'Exams retrieved successfully');
    }

    public function getUserExams(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 10) : 0;
        $searchQuery = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = Exam::query();

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', '%' . $searchQuery . '%');
            });
        }

        $query->orderBy($sortBy, $orderBy);

        $query->get()->filter(function ($exam) {
            return $exam->forMe();
        });

        $exams = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );

        return $this->successResponse(ExamResource::collection($exams)->response()->getData(), 'Exams retrieved successfully');
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
