<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGroupConfigRequest;
use App\Http\Resources\GroupConfigResource;
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

    public function update(UpdateGroupConfigRequest $request, $groupId)
    {
        $validated = $request->validated();
        if (isset($validated['tohfa']))
            $validated['tohfa'] = $validated['tohfa'] > 0 ? $validated['tohfa'] : null;

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
