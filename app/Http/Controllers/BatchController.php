<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use App\Http\Resources\BatchResource;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\SimpleBatchResource;
use App\Models\Group;
use App\Models\GroupMember;

class BatchController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $batches = Batch::all();

        return $this->successResponse(BatchResource::collection($batches), '');
    }
    public function indexAll(Request $request)
    {
        $batches = Batch::all();

        return $this->successResponse(SimpleBatchResource::collection($batches), '');
    }
    public function indexAdmin(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 10) : 0;
        $searchQuery = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = Batch::query();

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            });
        }

        $query->orderBy($sortBy, $orderBy);

        $batches = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );
        return $this->successResponse(BatchResource::collection($batches)->response()->getData(), 'barnds_retrieved_successfully');
    }

    public function show(Request $request, $id)
    {
        $batch = Batch::with('achievements')->find($id);

        if (!$batch) {
            return $this->errorResponse('الدفعة غير موجودة', 404);
        }

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

        return $this->successResponse(new BatchResource($batch), 'تمت إضافة الدفعة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('الدفعة غير موجودة', 404);
        }
        $request->validate([
            'name' => 'string',
            'submission_date' => 'date',
            'start_date' => 'date',
            'max_number' => 'integer',
            'gender' => 'string',

        ]);

        $batch->update($request->all());
        return $this->successResponse(new BatchResource($batch), 'تم تحديث الدفعة بنجاح');
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('الدفعة غير موجودة', 404);
        }

        if ($batch->name !== $request->get('name')) {
            return $this->errorResponse('لا يمكن حذف الدفعة', 400);
        }

        $batch->achievements()->detach();
        $batch->delete();

        return $this->successResponse(null, 'تم حذف الدفعة بنجاح');
    }

    public function batchMembers(Request $request, $id)
    {
        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('الدفعة غير موجودة', 404);
        }

        $perPage = $request->get('per_page', 10);

        $members = GroupMember::whereHas('group', function ($query) use ($id) {
            $query->where('batch_id', $id);
        })
            ->with('member')
            ->paginate($perPage);

        return $this->successResponse(GroupMemberResource::collection($members)->response()->getData(), '');
    }

    public function batchUsers(Request $request, $id)
    {
        $batch = Batch::find($id);
        if (!$batch) {
            return $this->errorResponse('الدفعة غير موجودة', 404);
        }

        $group = Group::find($request->group_id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }

        $perPage = $request->get('per_page', 10);

        $members = GroupMember::whereHas('group', function ($query) use ($id) {
            $query->where('batch_id', $id)->where('member_type', 'App\Models\User');
        })
            ->with('member')
            ->paginate($perPage);

        return $this->successResponse(GroupMemberResource::collection($members)->response()->getData(), '');
    }

}
