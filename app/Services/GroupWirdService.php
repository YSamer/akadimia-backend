<?php

namespace App\Services;

use App\Models\GroupWird;

class GroupWirdService
{
    public function getColumnForAction($action)
    {
        $columns = [
            'hifz' => 'hifz_page',
            'tafseer' => 'tafseer_dars',
            'tajweed' => 'tajweed_dars',
        ];

        return $columns[$action] ?? null;
    }

    public function generateAjazaNewData($lastGroupWird, $todayName, $newData)
    {
        $newData['tilawah_juz'] = $lastGroupWird->tilawah_juz + 1;
        $newData['sama_hizb'] = $lastGroupWird->sama_hizb + 1;
        $newData['weekly_tahder_from'] = $this->getWeeklyTahderFrom($todayName, $lastGroupWird);
        $newData['hifz_page'] = null;
        $newData['tajweed_dars'] = null;
        $newData['tafseer_dars'] = null;

        return $newData;
    }

    public function generateNewData($action, $lastGroupWird, $lastNonNullData, $todayName, $newData, $groupConfig)
    {
        $newData['tilawah_juz'] = $lastGroupWird->tilawah_juz + 1;
        $newData['sama_hizb'] = $lastGroupWird->sama_hizb + 1;
        $newData['weekly_tahder_from'] = $this->getWeeklyTahderFrom($todayName, $lastGroupWird);
        $newData['hifz_page'] = null;

        switch ($action) {
            case 'hifz':
                $newData['hifz_page'] = $lastNonNullData ? $lastNonNullData->hifz_page + 1 : 1;
                $newData['sard_shikh_from'] = $this->getSardShikhFrom($groupConfig);
                $newData['sard_rafiq_from'] = $this->getSardRafiqFrom($groupConfig);
                // $newData['hifz_tohfa_from'];
                break;
            case 'tafseer':
                $newData['tafseer_dars'] = $lastNonNullData ? $lastNonNullData->tafseer_dars + 1 : 1;
                break;
            case 'tajweed':
                $newData['tajweed_dars'] = $lastNonNullData ? $lastNonNullData->tajweed_dars + 1 : 1;
                break;
            default:
                break;
        }

        return $newData;
    }

    public function getSardShikhFrom($groupConfig)
    {
        // IF 'hifz' action
        $lastNonNullData = GroupWird::where('group_id', $groupConfig->group_id)
            ->whereNotNull('hifz_page')
            ->latest('date')->first();
        $startHifz = $groupConfig->hifz_start_from;
        $endHifz = $lastNonNullData->hifz_page;

        $fromToWird = getNextArrayFromContainingValue($startHifz, $endHifz, $groupConfig->sard_shikh, $lastNonNullData->sard_shikh_from);

        return $fromToWird;
    }

    public function getSardRafiqFrom($groupConfig)
    {
        // IF 'hifz' action
        $lastNonNullData = GroupWird::where('group_id', $groupConfig->group_id)
            ->whereNotNull('hifz_page')
            ->latest('date')->first();
        $startHifz = $groupConfig->hifz_start_from;
        $endHifz = $lastNonNullData->hifz_page;

        $fromToWird = getNextArrayFromContainingValue($startHifz, $endHifz, $groupConfig->sard_rafiq, $lastNonNullData->sard_rafiq_from);

        return $fromToWird;
    }

    public function getWeeklyTahderFrom($todayName, $lastGroupWird)
    {
        return $todayName === 'saturday'
            ? ($lastGroupWird->weekly_tahder_from === 1 ? 6 : $lastGroupWird->weekly_tahder_from + 5)
            : $lastGroupWird->weekly_tahder_from;
    }
}