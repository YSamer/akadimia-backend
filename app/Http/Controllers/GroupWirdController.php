<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupWirdResource;
use App\Models\GroupConfig;
use App\Models\GroupWird;
use App\Services\GroupWirdService;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupWirdController extends Controller
{
    use APIResponse;

    private GroupWirdService $groupWirdService;
    public function __construct(GroupWirdService $groupWirdService)
    {
        $this->groupWirdService = $groupWirdService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $group_id = $request->input('group_id');

        // $query = GroupWirdConfig::query(); //with('group');
        // if ($group_id) {
        //     $query->where('group_id', $group_id);
        // }
        // $configs = $query->get();

        // return $this->successResponse(GroupWirdConfigResource::collection($configs));
    }

    public function store(Request $request, $test = null)
    {
        $today = now()->format('Y-m-d');
        $todayName = strtolower(Carbon::now()->format('l'));

        $todayGroupWird = GroupWird::where('group_id', $request->group_id)
            ->where('date', $today)->first();
        if ($todayGroupWird) {
            return $this->errorResponse('تم تنزيل أوراد اليوم من قبل', new GroupWirdResource($todayGroupWird), 404);
        }

        $groupConfig = GroupConfig::where('group_id', $request->group_id)->first();
        if (!$groupConfig) {
            return $this->errorResponse('لا يوجد إعدادات لهذه المجموعة', 404);
        }

        $lastGroupWird = GroupWird::where('group_id', $request->group_id)
            ->latest('date')->first();

        $newData = [
            'group_id' => $request->group_id,
            'date' => $today,
            'hifz_page' => $request->hifz_page ?? 1,
            'tilawah_juz' => $request->tilawah_juz ?? 1,
            'sama_hizb' => $request->sama_hizb ?? 1,
            'tajweed_dars' => $request->tajweed_dars,
            'tafseer_dars' => $request->tafseer_dars,
            'weekly_tahder_from' => $request->weekly_tahder_from ?? 1,
            'sard_shikh_from' => $request->sard_shikh_from ?? null,
            'sard_rafiq_from' => $request->sard_rafiq_from ?? null,
            'hifz_tohfa_from' => $request->hifz_tohfa_from ?? null,
        ];

        if ($lastGroupWird) {
            $action = $groupConfig[$todayName] ?? null;
            if ($action === 'ajaza') {
                $newData = $this->groupWirdService->generateAjazaNewData($lastGroupWird, $todayName, $newData);
            } else if ($action === 'morajaa') {
                $newData = $this->groupWirdService->generateAjazaNewData($lastGroupWird, $todayName, $newData);
            } else {
                $lastNonNullData = GroupWird::where('group_id', $request->group_id)
                    ->whereNotNull($this->groupWirdService->getColumnForAction($action))
                    ->latest('date')->first();

                $newData = $this->groupWirdService->generateNewData($action, $lastGroupWird, $lastNonNullData, $todayName, $newData, $groupConfig);
            }
        }

        if ($test === 'test') {
            $groupWirdTodayTest = (new GroupWird())->fill($newData);
            return $this->successResponse(new GroupWirdResource($groupWirdTodayTest), 'عرض تنزيل أوراد اليوم');
        } else {
            $groupWirdToday = GroupWird::create($newData);
            return $this->successResponse(new GroupWirdResource($groupWirdToday), 'تم تنزيل أوراد اليوم بنجاح');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $today = now()->format('Y-m-d');
        $groupWirdToday = GroupWird::where('group_id', $id)
            ->where('date', $today)
            ->first();
        if (!$groupWirdToday) {
            return $this->errorResponse('لا يوجد أوراد اليوم لهذه المجموعة', 404);
        }
        return $this->errorResponse('تم تنزيل أوراد اليوم من قبل', new GroupWirdResource($groupWirdToday), 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $groupWirdConfig = GroupWirdConfig::find($id);

        // if (!$groupWirdConfig) {
        //     return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        // }

        // $validated = $request->validate([
        //     'group_id' => 'nullable|exists:groups,id',
        //     'title' => 'nullable|string',
        //     'description' => 'nullable|string',
        //     'section_type' => 'nullable|in:' . implode(',', array_column(SectionType::cases(), 'value')),
        //     'wird_type' => 'nullable|in:' . implode(',', array_column(WirdType::cases(), 'value')),
        //     'under_wird' => 'nullable|exists:group_wird_configs,id',
        //     'grade' => 'nullable|integer|min:1',
        //     'sanction' => 'nullable|integer|min:1',
        //     'is_repeated' => 'nullable|boolean',
        //     'is_changed' => 'nullable|boolean',
        //     'is_weekly_changed' => 'nullable|boolean',
        //     'from' => 'nullable|integer',
        //     'to' => 'nullable|integer',
        //     'start_from' => 'nullable|integer',
        //     'end_to' => 'nullable|integer',
        //     'change_value' => 'nullable|integer',
        //     'repeated_from_list' => 'nullable|exists:lists,id',
        //     'days' => 'nullable|array',
        //     'days.*' => 'in:' . implode(',', array_column(WeekDays::cases(), 'value')),
        // ]);

        // $groupWirdConfig->update($validated);

        // return $this->successResponse(new GroupWirdConfigResource($groupWirdConfig), 'تم تحديث اعداد للمجموعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $groupWirdConfig = GroupWirdConfig::find($id);

        // if (!$groupWirdConfig) {
        //     return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        // }

        // $groupWirdConfig->delete();
        // return $this->successResponse(null, 'تم حذف اعداد للمجموعة بنجاح');
    }
}
