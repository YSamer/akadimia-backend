<?php

namespace App\Http\Controllers;

use App\Models\BatchApply;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BatchApplyResource;

class BatchApplyController extends Controller
{
    use APIResponse;

    public function apply(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'batch_id' => 'required|integer',
        ]);

        $existingApply = BatchApply::where([
            'batch_id' => $request->batch_id,
            'user_id' => $user->id,
        ])->first();

        if ($existingApply) {
            return $this->successResponse(new BatchApplyResource($existingApply), 'تم التقدم بالفعل لهذه المرحلة');
        }

        $apply = BatchApply::create([
            'batch_id' => $request->batch_id,
            'user_id' => $user->id,
        ]);

        return $this->successResponse(new BatchApplyResource($apply), 'تم التقدم بنجاح');
    }

    public function achieve(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'batch_id' => 'required|integer',
            'achievement_id' => 'required|integer',
        ]);

        $apply = BatchApply::where([
            'batch_id' => $request->batch_id,
            'user_id' => $user->id,
        ])->first();

        if (!$apply) {
            return $this->errorResponse('لا يوجد تقدم لهذه المرحلة', 404);
        }

        if (!in_array($request->achievement_id, $apply->achievement_ids ?? [])) {
            $apply->achievement_ids = array_merge($apply->achievement_ids ?? [], [$request->achievement_id]);
            $apply->save();

            return $this->successResponse(new BatchApplyResource($apply), 'تم التحديث بنجاح');
        }

        return $this->successResponse(new BatchApplyResource($apply), 'الإنجاز موجود بالفعل');
    }

    public function unachieve(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'batch_id' => 'required|integer',
            'achievement_id' => 'required|integer',
        ]);

        $apply = BatchApply::where([
            'batch_id' => $request->batch_id,
            'user_id' => $user->id,
        ])->first();

        if (!$apply) {
            return $this->errorResponse('لا يوجد تقدم لهذه المرحلة', 404);
        }

        if (in_array($request->achievement_id, $apply->achievement_ids ?? [])) {
            $apply->achievement_ids = array_filter($apply->achievement_ids, function ($id) use ($request) {
                return $id != $request->achievement_id;
            });

            $apply->achievement_ids = array_values($apply->achievement_ids);
            $apply->save();
            return $this->successResponse(new BatchApplyResource($apply), 'تم إلغاء التحديث بنجاح');
        }

        return $this->successResponse(new BatchApplyResource($apply), 'الإنجاز غير موجود');
    }

}
