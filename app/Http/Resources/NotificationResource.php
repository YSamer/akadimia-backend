<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'read_at' => $this->read_at,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'created_at' => $this->created_at,
        ];
    }
}
