<?php

// namespace App\Http\Controllers;

// use App\Enums\SectionType;
// use App\Enums\WeekDays;
// use App\Enums\WirdType;
// use App\Http\Resources\GroupWirdConfigResource;
// use App\Models\GroupWirdConfig;
// use App\Traits\APIResponse;
// use Illuminate\Http\Request;

// class GroupWirdController extends Controller
// {
//     use APIResponse;

//     /**
//      * Display a listing of the resource.
//      */
//     public function index(Request $request)
//     {
//         $group_id = $request->input('group_id');

//         $query = GroupWirdConfig::query(); //with('group');
//         if ($group_id) {
//             $query->where('group_id', $group_id);
//         }
//         $configs = $query->get();

//         return $this->successResponse(GroupWirdConfigResource::collection($configs));
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'group_id' => 'required|exists:groups,id',
//             'title' => 'nullable|string',
//             'description' => 'nullable|string',
//             'section_type' => 'required|in:' . implode(',', array_column(SectionType::cases(), 'value')),
//             'wird_type' => 'required|in:' . implode(',', array_column(WirdType::cases(), 'value')),
//             'under_wird' => 'nullable|exists:group_wird_configs,id',
//             'grade' => 'nullable|integer|min:1',
//             'sanction' => 'nullable|integer|min:1',
//             'is_repeated' => 'nullable|boolean',
//             'is_changed' => 'nullable|boolean',
//             'is_weekly_changed' => 'nullable|boolean',
//             'from' => 'nullable|integer',
//             'to' => 'nullable|integer',
//             'start_from' => 'nullable|integer',
//             'end_to' => 'nullable|integer',
//             'change_value' => 'nullable|integer',
//             'repeated_from_list' => 'nullable|exists:lists,id',
//             'days' => 'nullable|array',
//             'days.*' => 'in:' . implode(',', array_column(WeekDays::cases(), 'value')),
//         ]);

//         $config = GroupWirdConfig::create($validated);

//         return $this->successResponse(new GroupWirdConfigResource($config), 'تم إنشاء اعداد للمجموعة بنجاح');
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show($id)
//     {
//         $groupWirdConfig = GroupWirdConfig::with('group')->find($id);

//         if (!$groupWirdConfig) {
//             return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
//         }

//         return $this->successResponse(new GroupWirdConfigResource($groupWirdConfig));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, $id)
//     {
//         $groupWirdConfig = GroupWirdConfig::find($id);

//         if (!$groupWirdConfig) {
//             return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
//         }

//         $validated = $request->validate([
//             'group_id' => 'nullable|exists:groups,id',
//             'title' => 'nullable|string',
//             'description' => 'nullable|string',
//             'section_type' => 'nullable|in:' . implode(',', array_column(SectionType::cases(), 'value')),
//             'wird_type' => 'nullable|in:' . implode(',', array_column(WirdType::cases(), 'value')),
//             'under_wird' => 'nullable|exists:group_wird_configs,id',
//             'grade' => 'nullable|integer|min:1',
//             'sanction' => 'nullable|integer|min:1',
//             'is_repeated' => 'nullable|boolean',
//             'is_changed' => 'nullable|boolean',
//             'is_weekly_changed' => 'nullable|boolean',
//             'from' => 'nullable|integer',
//             'to' => 'nullable|integer',
//             'start_from' => 'nullable|integer',
//             'end_to' => 'nullable|integer',
//             'change_value' => 'nullable|integer',
//             'repeated_from_list' => 'nullable|exists:lists,id',
//             'days' => 'nullable|array',
//             'days.*' => 'in:' . implode(',', array_column(WeekDays::cases(), 'value')),
//         ]);

//         $groupWirdConfig->update($validated);

//         return $this->successResponse(new GroupWirdConfigResource($groupWirdConfig), 'تم تحديث اعداد للمجموعة بنجاح');
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         $groupWirdConfig = GroupWirdConfig::find($id);

//         if (!$groupWirdConfig) {
//             return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
//         }

//         $groupWirdConfig->delete();
//         return $this->successResponse(null, 'تم حذف اعداد للمجموعة بنجاح');
//     }
// }
