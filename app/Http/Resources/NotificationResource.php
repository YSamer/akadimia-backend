<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        Carbon::setLocale('ar');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'read_at' => $this->read_at ? $this->read_at->format('Y-m-d H:i:s') : null,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans() : null,
        ];
    }
}
