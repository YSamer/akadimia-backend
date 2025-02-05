<?php

namespace App\Services;


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

    public function generateNewData($action, $lastGroupWird, $lastNonNullData, $todayName, $newData)
    {
        $newData['tilawah_juz'] = $lastGroupWird->tilawah_juz + 1;
        $newData['sama_hizb'] = $lastGroupWird->sama_hizb + 1;
        $newData['weekly_tahder_from'] = $this->getWeeklyTahderFrom($todayName, $lastGroupWird);
        $newData['hifz_page'] = null;

        switch ($action) {
            case 'hifz':
                $newData['hifz_page'] = $lastNonNullData ? $lastNonNullData->hifz_page + 1 : 1;
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

    public function getWeeklyTahderFrom($todayName, $lastGroupWird)
    {
        return $todayName === 'saturday'
            ? ($lastGroupWird->weekly_tahder_from === 1 ? 6 : $lastGroupWird->weekly_tahder_from + 5)
            : $lastGroupWird->weekly_tahder_from;
    }
}