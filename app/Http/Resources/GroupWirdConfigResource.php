<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupWirdConfigResource extends JsonResource
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
            'title' => $this->getTitle(),
            'description' => $this->description,
            'section_type' => $this->section_type,
            'wird_type' => $this->wird_type,
            'under_wird' => $this->under_wird,
            'grade' => $this->grade,
            'sanction' => $this->sanction,
            'is_repeated' => $this->is_repeated,
            'is_changed' => $this->is_changed,
            'is_weekly_changed' => $this->is_weekly_changed,
            'from' => $this->from,
            'to' => $this->to,
            'start_from' => $this->start_from,
            'end_to' => $this->end_to,
            'change_value' => $this->change_value,
            'repeated_from_list' => $this->repeated_from_list,
            'days' => $this->days,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
