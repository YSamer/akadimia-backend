<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishGroupHalaqahRequest;
use App\Models\Group;
use App\Models\Halaqah;
use App\Models\Teacher;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HalaqahController extends Controller
{
    use APIResponse;

    public function finishHalaqah(FinishGroupHalaqahRequest $request)
    {
        $teacher = Auth::guard('teacher')->user();
        if (!($teacher instanceof Teacher)) {
            return $this->errorResponse('ْUnAuthrize', null, 403);
        }
        $group = Group::find($request->group_id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', null, 404);
        }
        // TODO: if teacher in group members

        $existHalaqah = Halaqah::where('type', 'halaqah')
            ->where('teacher_id', $request->teacher_id)
            ->where('target_type', Group::class)
            ->where('target_id', $request->group_id)
            ->first();

        if ($existHalaqah) {
            return $this->errorResponse('تم إضافة الحلقة من قبل', null, 404);
        }
        // TODO: add DB::tr..
        $halaqah = Halaqah::create([
            'type' => 'halaqah',
            'teacher_id' => $request->teacher_id,
            'target_type' => Group::class,
            'target_id' => $request->group_id,
            'duration_hours' => $request->duration_hours,
            'duration_minutes' => $request->duration_minutes,
            'date' => $request->date,
        ]);

        // TODO: Add Grade for all groups students
        
        // TODO: send notification to group members (if exist)

        return $this->successResponse($halaqah, 'تم إضافة الحلقة بنجاح');
    }
}
