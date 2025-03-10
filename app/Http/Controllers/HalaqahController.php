<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishGroupHalaqahRequest;
use App\Http\Requests\FinishStudentSardRequest;
use App\Models\Group;
use App\Models\Halaqah;
use App\Models\Notification;
use App\Models\Teacher;
use App\Models\User;
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

        if (!$group->teachers->contains($teacher)) {
            return $this->errorResponse('لا يمكنك إنشاء قائمة الحلقة لهذه المجموعة', null, 403);
        }

        $existingHalaqah = Halaqah::where([
            'type' => 'halaqah',
            'teacher_id' => $teacher->id,
            'target_type' => Group::class,
            'target_id' => $request->group_id
        ])->first();

        if ($existingHalaqah) {
            return $this->errorResponse('تم إضافة قائمة الحلقة من قبل', null, 404);
        }

        try {
            DB::beginTransaction();
            $halaqah = Halaqah::create([
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
                    $notifications[] = [
                        'user_id' => $user->id,
                        'user_type' => User::class,
                        'title' => 'تم إضافة درجة الحلقة',
                        'body' => 'لقد حصلت على تقييم ' . $halaqahGrade . ' من خلال المعلم ' . $teacher->name,
                    ];
                }
            }
            foreach ($notifications as $notification) {
                Notification::create($notification);
            }
            DB::commit();
            return $this->successResponse($halaqah, 'تم إضافة قائمة الحلقة بنجاح');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى', null, 500);
        }
    }

    public function finishSard(FinishStudentSardRequest $request)
    {
        $teacher = Auth::guard('teacher')->user();
        if (!($teacher instanceof Teacher)) {
            return $this->errorResponse('ْUnAuthrize', null, 403);
        }
        $group = Group::find($request->group_id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', null, 404);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return $this->errorResponse('الطالب غير موجودة', null, 404);
        }

        if (!$group->teachers->contains($teacher)) {
            return $this->errorResponse('لا يمكنك إضافة درجة السرد لهذا الطالب', null, 403);
        }

        if (!$group->users->contains($user)) {
            return $this->errorResponse('لا يمكنك إضافة درجة السرد لهذا الطالب', null, 403);
        }
        // TODO: يمكن أن يوجد الطالب بأكثر من جروب يمكن عمل موديل لكل نوع حلقة
        $existingHalaqah = Halaqah::where([
            'type' => 'sard',
            'teacher_id' => $teacher->id,
            'target_type' => User::class,
            'target_id' => $request->user_id
        ])->first();

        // if ($existingHalaqah) {
        //     return $this->errorResponse('تم إضافة قائمة الحلقة من قبل', null, 404);
        // }

        try {
            DB::beginTransaction();
            $halaqah = $existingHalaqah ?? Halaqah::create([
                'type' => 'sard',
                'teacher_id' => $teacher->id,
                'target_type' => User::class,
                'target_id' => $request->user_id,
                'duration_hours' => $request->duration_hours,
                'duration_minutes' => $request->duration_minutes,
                'date' => $request->date,
            ]);
            $halaqahGrade = $request->grade;
            $wirdDone = $user->wirdDones->where('date', Carbon::parse($request->date))->first();
            if (!$wirdDone) {
                $user->wirdDones()->create([
                    'group_id' => $request->group_id,
                    'date' => $request->date,
                    'sard_shikh_grade' => $halaqahGrade,
                ]);
            } else {
                $wirdDone->sard_shikh_grade = $halaqahGrade;
                $wirdDone->save();
            }
            Notification::create([
                'user_id' => $user->id,
                'user_type' => User::class,
                'title' => 'تم إضافة درجة سرد المعلم',
                'body' => 'لقد حصلت على تقييم ' . $halaqahGrade . ' من خلال المعلم ' . $teacher->name,
            ]);
            DB::commit();
            return $this->successResponse($halaqah, 'تم إضافة قائمة الحلقة بنجاح');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى', null, 500);
        }
    }
}
