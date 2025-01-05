<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // 'user_id',
        // 'image',
        // 'is_confirm',
        // 'confirmed_by',

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'amount' => $this->amount,
            'image' => $this->image,
            'is_confirm' => $this->is_confirm,
            'confirmed_by' => $this->confirmed_by,
            'confirmed_by_name' => $this->confirmed_by ? $this->admin->name : null,
            // 'confirmed_by' => $this->confirmed_by ? new AdminResource($this->whenLoaded('admin')) : null,

            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
