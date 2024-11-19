<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'group' => new GroupResource($this->whenLoaded('group')), // Include related group if loaded
            'title' => $this->title,
            'amount' => $this->amount,
            'from' => $this->from,
            'to' => $this->to,
            'wird_type' => $this->wird_type,
            'section_type' => $this->section_type,
            'score' => $this->score,
            'day' => $this->day,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
