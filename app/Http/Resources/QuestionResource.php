<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'question' => $this->question,
            'is_required' => $this->is_required,
            'grade' => $this->grade,
            'options' => OptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
