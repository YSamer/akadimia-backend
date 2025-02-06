<?php

namespace App\Http\Resources;

use App\Models\GroupConfig;
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
        $groupConfig = GroupConfig::where('group_id', $this->group_id)->first();
        $sardShikhTo = 5;
        $sardRafiqTo = 5;
        $hifzTohfaTo = 5;
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
            'sard_shikh_from' => $this->sard_shikh_from,
            'sard_shikh_to' => $sardShikhTo,
            'sard_rafiq_from' => $this->sard_rafiq_from,
            'sard_rafiq_to' => $sardRafiqTo,
            'hifz_tohfa_from' => $this->hifz_tohfa_from,
            'hifz_tohfa_to' => $hifzTohfaTo,
        ];
    }
}
