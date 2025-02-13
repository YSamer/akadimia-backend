<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishGroupHalaqahRequest;
use App\Models\Group;
use App\Models\Halaqah;
use App\Models\Teacher;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->where('teacher_id', $teacher->id)
            ->where('target_type', Group::class)
            ->where('target_id', $request->group_id)
            ->first();

        if ($existHalaqah) {
            return $this->errorResponse('تم إضافة قائمة الحلقة من قبل', null, 404);
        }

        try {
            DB::beginTransaction();
            $halaqah = Halaqah::updateOrCreate([
                'type' => 'halaqah',
                'teacher_id' => $teacher->id,
                'target_type' => Group::class,
                'target_id' => $request->group_id,
                'duration_hours' => $request->duration_hours,
                'duration_minutes' => $request->duration_minutes,
                'date' => $request->date,
            ]);

            $users = $group->users;
            foreach ($users as $user) {
                $halaqahGradeKey = 'user_' . $user->id;
                $halaqahGrade = $request->$halaqahGradeKey;
                if ($halaqahGrade !== null) {
                    $wirdDone = $user->wirdDones->where('date', Carbon::parse($request->date))->first();
                    if (!$wirdDone) {
                        $user->wirdDones()->create([
                            'group_id' => $request->group_id,
                            'date' => $request->date,
                            'halaqah_grade' => $halaqahGrade,
                        ]);
                    } else {
                        $wirdDone->halaqah_grade = $halaqahGrade;
                        $wirdDone->save();
                    }
                }
            }

            // TODO: send notification to group members (if exist)

            DB::commit();
            return $this->successResponse($halaqah, 'تم إضافة قائمة الحلقة بنجاح');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى', null, 500);
        }
    }
}
