<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupConfigResource;
use App\Models\GroupConfig;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class GroupConfigController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $group_id = $request->input('group_id');

        $groupConfigs = GroupConfig::with('group')
            ->where('group_id', $group_id)->get();
        return $this->successResponse(GroupConfigResource::collection($groupConfigs));
    }

    public function show($id)
    {
        $groupConfig = GroupConfig::with('group')->find($id);

        if (!$groupConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        return $this->successResponse(new GroupConfigResource($groupConfig));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'nullable|string',
            'amount' => 'nullable|integer',
            'from' => 'nullable|integer',
            'to' => 'nullable|integer',
            'wird_type' => 'required|string',
            'section_type' => 'required|string',
            'score' => 'nullable|numeric',
            'day' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ]);

        $groupConfig = GroupConfig::create($validatedData);

        return $this->successResponse(new GroupConfigResource($groupConfig), 'تم إنشاء اعداد للمجموعة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $groupConfig = GroupConfig::find($id);

        if (!$groupConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        $validatedData = $request->validate([
            'title' => 'nullable|string',
            'amount' => 'nullable|integer',
            'from' => 'nullable|integer',
            'to' => 'nullable|integer',
            'wird_type' => 'nullable|string',
            'section_type' => 'nullable|string',
            'score' => 'nullable|numeric',
            'day' => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ]);

        $groupConfig->update($validatedData);

        return $this->successResponse(new GroupConfigResource($groupConfig), 'تم تحديث اعداد للمجموعة بنجاح');
    }

    public function destroy($id)
    {
        $groupConfig = GroupConfig::find($id);
        
        if (!$groupConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        $groupConfig->delete();
        return $this->successResponse(null, 'تم حذف اعداد للمجموعة بنجاح');
    }
}
