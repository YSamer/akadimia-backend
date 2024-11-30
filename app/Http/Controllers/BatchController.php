<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use App\Http\Resources\BatchResource;

class BatchController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $batches = Batch::all();

        return $this->successResponse(BatchResource::collection($batches), '');
    }

    public function show(Request $request, $id)
    {
        $batch = Batch::find($id);

        if (!$batch) {
            return $this->errorResponse('المرحلة غير موجودة', 404);
        }

        $batch->load('achievements');

        return $this->successResponse(new BatchResource($batch, true), '');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'submission_date' => 'required|date',
            'start_date' => 'nullable|date',
            'max_number' => 'required|integer',
            'gender' => 'required|string',
        ]);

        $batch = Batch::create($request->all());

        return $this->successResponse(new BatchResource($batch), 'تمت إضافة المرحلة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('المرحلة غير موجودة', 404);
        }
        $request->validate([
            'name' => 'string',
            'submission_date' => 'date',
            'start_date' => 'date',
            'max_number' => 'integer',
            'gender' => 'string',
        ]);

        $batch->update($request->all());
        return $this->successResponse(new BatchResource($batch), 'تم تحديث المرحلة بنجاح');
    }

    public function destroy($id)
    {
        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('المرحلة غير موجودة', 404);
        }
        $batch->delete();

        return $this->successResponse(null, 'تم حذف المرحلة بنجاح');
    }
}
