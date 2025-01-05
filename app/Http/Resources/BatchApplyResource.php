<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchApplyResource extends JsonResource
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
            'batch_id' => $this->batch_id,
            'status' => $this->status,
            'achievement_ids' => $this->achievement_ids,
            'payment_id' => $this->payment_id,
            'payment' => $this->payment ? new PaymentResource($this->payment) : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,

            'user' => new UserResource($this->whenLoaded('user')),
            'batch' => new BatchResource($this->whenLoaded('batch')),
        ];
    }
}
