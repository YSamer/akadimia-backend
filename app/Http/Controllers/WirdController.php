<?php

namespace App\Http\Controllers;

use App\Http\Resources\WirdDoneResource;
use Illuminate\Http\Request;
use App\Http\Resources\GroupWirdConfigResource;
use App\Http\Resources\WirdResource;
use App\Models\GroupWirdConfig;
use App\Models\Wird;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WirdController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $group_id = $request->input('group_id');
        $query = Wird::query();
        if ($group_id) {
            $query->where('group_id', $group_id);
        }
        $wirds = $query->get();

        return $this->successResponse(WirdResource::collection($wirds));
    }

    public function setTodayWirds(Request $request)
    {
        $group_id = $request->input('group_id');
        // $today = now()->format('Y-m-d');
        $dayName = now()->dayName;

        $configs = GroupWirdConfig::
            whereJsonContains('days', strtolower($dayName))
            ->get();
        if ($group_id) {
            $configs = $configs->where('group_id', $group_id);
        }

        if ($configs->isEmpty()) {
            return $this->errorResponse('لا يوجد إعداد أوراد لليوم.');
        }

        DB::beginTransaction();
        try {
            foreach ($configs as $config) {
                // Check if the group has a Wird for today
                $existingWird = Wird::where('group_id', $config->group_id)
                    ->where('group_wird_config_id', $config->id)
                    ->where('date', now()->format('Y-m-d'))
                    ->first();

                if ($existingWird) {
                    continue;
                }
                Wird::create([
                    'group_id' => $config->group_id,
                    'group_wird_config_id' => $config->id,
                    'date' => now()->format('Y-m-d'),
                    'title' => $config->title,
                    'start_from' => $config->start_from,
                    'end_to' => $config->end_to,
                    // 'file_path' => 
                    // 'url' => 
                ]);
                // Change to next day config->start_from, config->end_to with change_value must between from, to
                // if config->is_repeated is false || config->is_changed is false, do nothing else update it.
                if ($config->is_repeated && $config->is_changed) {
                    $start_from = $config->start_from + $config->change_value;
                    $end_to = $config->end_to + $config->change_value;
                    $config->start_from = $start_from > $config->to ?
                        $start_from - $config->to + ($config->from - 1)
                        : $start_from;
                    $config->end_to = $config->end_to !== null ? ($end_to > $config->to ?
                        $end_to - $config->to + ($config->from - 1)
                        : $end_to) : null;
                    $config->save();
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }


        return $this->successResponse(GroupWirdConfigResource::collection($configs));
    }

    public function todayWirds(Request $request)
    {
        $group_id = $request->input('group_id');

        $today = now()->format('Y-m-d');
        $wirds = Wird::where('date', $today)->get();
        if ($group_id) {
            $wirds = $wirds->where('group_id', $group_id);
        }
        return $this->successResponse(WirdResource::collection($wirds));
    }

    public function groupTodayWirds(Request $request, $groupId)
    {
        $today = now()->format('Y-m-d');
        $wirds = Wird::where('date', $today)
            ->where('group_id', $groupId)->get();

        return $this->successResponse(WirdResource::collection($wirds));
    }

    public function groupTodayWirdsStudent(Request $request, $groupId)
    {
        $today = now()->format('Y-m-d');
        $wirds = Wird::where('date', $today)
            ->where('group_id', $groupId)->get();

        $wirdsDone = Auth::user()->wirdDones()
            ->whereIn('wird_id', $wirds->pluck('id'))
            ->get();

        $totalGrades = $wirds->sum('grade');
        $currentGrades = $wirdsDone->sum('grade');
        return $this->successResponse([
            'wirds' => WirdResource::collection($wirds),
            'wirds_done' => WirdDoneResource::collection($wirdsDone->except(['wird'])),
            'total_grade' => $totalGrades,
            'current_grade' => $currentGrades,
            'percentage' => $currentGrades / $totalGrades * 100,
        ]);
    }
}
