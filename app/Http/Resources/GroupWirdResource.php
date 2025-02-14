<?php

namespace App\Http\Resources;

use App\Models\GroupConfig;
use App\Models\UserWirdsDone;
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


        $data = [
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
            'sard_shikh' => $this->sard_shikh,
            'sard_rafiq' => $this->sard_rafiq,
            'hifz_tohfa_from' => $this->hifz_tohfa_from,
            'sard_shikh_confing' => $groupConfig->sard_shikh,
            'sard_rafiq_confing' => $groupConfig->sard_rafiq,
        ];
        if (auth('user')->check()) {
            $data['user_wird_done'] = new UserWirdsDoneResource(UserWirdsDone::where([
                'group_id' => $this->group_id,
                'user_id' => auth('user')->user()->id,
                'date' => $this->date,
            ])->first());
        }

        return $data;
    }
}
