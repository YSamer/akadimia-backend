<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupWirdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'date' => $this->date,
            'hifz_page' => $this->hifz_page,
            'tilawah_juz' => $this->tilawah_juz,
            'sama_hizb' => $this->sama_hizb,
            'weekly_tahder_from' => $this->weekly_tahder_from,
            'weekly_tahder_to' => $this->weekly_tahder_to,
            'tajweed_dars' => $this->tajweed_dars,
            'tafseer_dars' => $this->tafseer_dars,
        ];
    }
}
