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

    public function generateNewData($request, $action, $lastGroupWird, $lastNonNullData, $todayName, $newData, $groupConfig)
    {
        $newData['tilawah_juz'] = $lastGroupWird->tilawah_juz + 1;
        $newData['sama_hizb'] = $lastGroupWird->sama_hizb + 1;
        $newData['weekly_tahder_from'] = $this->getWeeklyTahderFrom($todayName, $lastGroupWird);
        $newData['hifz_page'] = null;

        switch ($action) {
            case 'hifz':
                $newData['hifz_page'] = $lastNonNullData ? $lastNonNullData->hifz_page + 1 : 1;
                $newData['sard_shikh'] = $this->getSardShikh($request, $groupConfig);
                $newData['sard_rafiq'] = $this->getSardRafiq($request, $groupConfig);
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

    public function getSardShikh($request, $groupConfig)
    {
        $sardShikh = null;
        $lastNonNullData = GroupWird::where('group_id', $groupConfig->group_id)
            ->whereNotNull('hifz_page')
            ->latest('date')->first();
        $startHifz = $groupConfig->hifz_start_from;
        $endHifz = $lastNonNullData->hifz_page;
        if ($groupConfig->sard_shikh === 'last_ten_pages') {
            $from = $endHifz - 9;
            if ($from <= 0)
                $from = 1;
            $sardShikh = [
                'type' => 'last_ten_pages',
                'from' => $from,
                'to' => $endHifz,
            ];
        } else if ($groupConfig->sard_shikh === 'last_juz') {
            $from = $endHifz - 19;
            if ($from <= 0)
                $from = 1;
            $sardShikh = [
                'type' => 'last_juz',
                'from' => $from,
                'to' => $endHifz,
            ];
        } else if ($groupConfig->sard_shikh === 'custom') {
            $sardShikh = [
                'type' => 'custom',
                'from' => (int) $request->sard_shikh_from ?? $endHifz - 9,
                'to' => (int) $request->sard_shikh_to ?? $endHifz,
            ];
        } else if ($groupConfig->sard_shikh === 'sequent_hifz') {
            $lastSard = $lastNonNullData->sard_shikh;
            $nextHizb = getHizbByPageInRange(isset($lastSard['to']) ? $lastSard['to'] + 1 : $startHifz, $startHifz, $endHifz);
            $sardShikh = [
                'type' => 'sequent_hifz',
                'from' => $nextHizb['start_page'],
                'to' => min($endHifz, $nextHizb['end_page']),
            ];
        }

        return $sardShikh;
    }

    public function getSardRafiq($request, $groupConfig)
    {
        $sardRafiq = null;
        $lastNonNullData = GroupWird::where('group_id', $groupConfig->group_id)
            ->whereNotNull('hifz_page')
            ->latest('date')->first();
        $startHifz = $groupConfig->hifz_start_from;
        $endHifz = $lastNonNullData->hifz_page;
        if ($groupConfig->sard_rafiq === 'last_ten_pages') {
            $from = $endHifz - 9;
            if ($from <= 0)
                $from = 1;
            $sardRafiq = [
                'type' => 'last_ten_pages',
                'from' => $from,
                'to' => $endHifz,
            ];
        } else if ($groupConfig->sard_rafiq === 'last_juz') {
            $from = $endHifz - 19;
            if ($from <= 0)
                $from = 1;
            $sardRafiq = [
                'type' => 'last_juz',
                'from' => $from,
                'to' => $endHifz,
            ];
        } else if ($groupConfig->sard_rafiq === 'custom') {
            $sardRafiq = [
                'type' => 'custom',
                'from' => (int) $request->sard_rafiq_from ?? $endHifz - 9,
                'to' => (int) $request->sard_rafiq_to ?? $endHifz,
            ];
        } else if ($groupConfig->sard_rafiq === 'sequent_hifz') {
            $lastSard = $lastNonNullData->sard_rafiq;
            $nextHizb = getHizbByPageInRange(isset($lastSard['to']) ? $lastSard['to'] + 1 : $startHifz, $startHifz, $endHifz);
            $sardRafiq = [
                'type' => 'sequent_hifz',
                'from' => $nextHizb['start_page'],
                'to' => min($endHifz, $nextHizb['end_page']),
            ];
        }

        return $sardRafiq;
    }

    public function getWeeklyTahderFrom($todayName, $lastGroupWird)
    {
        return $todayName === 'saturday'
            ? ($lastGroupWird->weekly_tahder_from === 1 ? 6 : $lastGroupWird->weekly_tahder_from + 5)
            : $lastGroupWird->weekly_tahder_from;
    }
}