<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ? $this->name : $this->numberToArabicOrdinal(),
            'submission_date' => $this->submission_date ? $this->submission_date->format('Y-m-d') : null,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'max_number' => $this->max_number,
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
