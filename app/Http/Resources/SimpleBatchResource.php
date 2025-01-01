<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BatchApplyResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\MyFunctions;

class SimpleBatchResource extends JsonResource
{
    use MyFunctions;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ? $this->name : $this->numberToArabicOrdinalFemale($this->id),
        ];
    }
}
