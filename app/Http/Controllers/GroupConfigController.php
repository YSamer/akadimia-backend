<?php

namespace App\Http\Controllers;

use App\Enums\SectionType;
use App\Enums\WeekDays;
use App\Enums\WirdType;
use App\Http\Resources\GroupConfigResource;
use App\Http\Resources\GroupWirdConfigResource;
use App\Models\Group;
use App\Models\GroupConfig;
use App\Models\GroupWirdConfig;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class GroupConfigController extends Controller
{
    use APIResponse;


    public function show($groupId)
    {
        $group = Group::find($groupId);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }
        $groupConfig = GroupConfig::where('group_id', $groupId)->first();

        if (!$groupConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        return $this->successResponse(new GroupConfigResource($groupConfig));
    }

    public function update(Request $request, $groupId)
    {
        $fillableAttributes = (new GroupConfig())->getFillable();
        $rules = [];
        foreach ($fillableAttributes as $attribute) {
            if ($attribute !== 'group_id' && $attribute !== 'id') {
                $rules[$attribute] = 'nullable|integer|between:1,20';
            }
        }

        $validated = $request->validate($rules);

        $group = Group::find($groupId);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }
        $groupConfig = GroupConfig::where('group_id', $groupId)->first();

        if (!$groupConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        $groupConfig->update($validated);

        return $this->successResponse(new GroupConfigResource($groupConfig), 'تم تعديل الإعدادات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $groupWirdConfig = GroupWirdConfig::find($id);

        if (!$groupWirdConfig) {
            return $this->errorResponse('اعدادات المجموعة غير موجودة', 404);
        }

        $groupWirdConfig->delete();
        return $this->successResponse(null, 'تم حذف اعداد للمجموعة بنجاح');
    }
}
