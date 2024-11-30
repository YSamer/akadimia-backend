<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
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
            'group_id' => $this->group_id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'member' => $this->whenLoaded('member', function () {
                return [
                    'id' => $this->member->id,
                    'name' => $this->member->name,
                    'email' => $this->member->email ?? null,
                    'phone' => $this->member->phone ?? null,
                    'gender' => $this->member->gender ?? null,
                    'birth_date' => $this->member->birth_date ? $this->member->birth_date->format('Y-m-d H:i:s') : null,
                    'image' => $this->member->image ?? null,
                ];
            }),
        ];
    }
}
