<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WirdDoneDetailsResource extends JsonResource
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
            'user_id' => $this->user_id,
            'wird_id' => $this->wird_id,
            'is_completed' => $this->is_completed,
            'score' => $this->score,
            'user' => new UserResource($this->whenLoaded('user')),
            'wird' => new WirdResource($this->whenLoaded('wird')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
