<?php

namespace App\Http\Resources;

use App\Models\Group;
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
            'id' => $this->member->id,
            'name' => $this->member->name,
            'email' => $this->member->email ?? null,
            'phone' => $this->member->phone ?? null,
            'gender' => $this->member->gender ?? null,
            'birth_date' => $this->member->birth_date ? $this->member->birth_date->format('Y-m-d H:i:s') : null,
            'image' => $this->member->image ?? null,
            'group_id' => $this->group_id ?: null,
            'member_id' => $this->id,
            'member_type' => $this->memberType(),
        ];
    }
}
