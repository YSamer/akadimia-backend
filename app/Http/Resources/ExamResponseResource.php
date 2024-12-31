<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResponseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'question_id' => $this->question_id,
            'user_id' => $this->user_id,
            'response' => json_decode($this->response),
            'question' => new QuestionResource($this->whenLoaded('question')),
        ];
    }
}
