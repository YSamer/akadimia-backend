<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
        $guard = Auth::getDefaultDriver();

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'forwardable_type' => class_basename($this->forwardable_type),
            'forwardable_id' => $this->forwardable_id,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_grade' => $this->totalGrade(),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
        if ($guard === 'user') {
            $data['my_grade'] = $this->userGrade();
            // $data['for_me'] = $this->forMe();
        }

        return $data;
    }
}

