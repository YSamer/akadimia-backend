<?php

namespace App\Http\Controllers;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupMember;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    use APIResponse;

    public function index()
    {
        $groups = Group::with('batch')->paginate(10);
        return $this->successResponse(GroupResource::collection($groups));
    }

    public function indexAdmin(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 10) : 0;
        $searchQuery = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');
        $batch = $request->input('batch_id');

        $query = Group::query();

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($batch) {
            $query->where('batch_id', $batch);
        }

        $query->orderBy($sortBy, $orderBy);

        $groups = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );
        return $this->successResponse(GroupResource::collection($groups)->response()->getData(), 'barnds_retrieved_successfully');
    }

    public function show($id)
    {
        $group = Group::with(['batch', 'members.member'])->find($id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }
        return $this->successResponse(new GroupResource($group));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'batch_id' => 'required|exists:batches,id',
        ]);

        $data = $request->only(['name', 'batch_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('groups', 'public');
        }

        $group = Group::create($data);

        return $this->successResponse(new GroupResource($group), 'تمت إضافة المجموعة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'batch_id' => 'nullable|exists:batches,id',
        ]);

        $data = $request->only(['name', 'batch_id']);

        if ($request->hasFile('image')) {
            if ($group->image) {
                Storage::disk('public')->delete($group->image);
            }
            $data['image'] = $request->file('image')->store('groups', 'public');
        }

        $group->update($data);
        return $this->successResponse(new GroupResource($group), 'تم تحديث المجموعة بنجاح');
    }

    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }
        if ($group->image) {
            Storage::disk('public')->delete($group->image);
        }
        $group->delete();
        return $this->successResponse(null, 'تم حذف المجموعة بنجاح');
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'member_id' => 'required|integer',
            'member_type' => 'required|string|in:User,Teacher,Admin',
        ]);

        $group = Group::find($request->group_id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }

        $model = "App\\Models\\" . $request->member_type;
        if (!class_exists($model)) {
            return $this->errorResponse("نوع العضو غير متاح.", 422);
        }

        $member = $model::find($request->member_id);
        if (!$member) {
            return $this->errorResponse("العضو غير موجود.", 404);
        }

        // Check if the member is already in the group
        $exists = GroupMember::where([
            'group_id' => $request->group_id,
            'member_id' => $request->member_id,
            'member_type' => $model,
        ])->exists();

        if ($exists) {
            return $this->errorResponse("هذا العضو موجود بالفعل داخل المجموعة.", 409);
        }

        // Add member to the group
        $groupMember = GroupMember::create([
            'group_id' => $group->id,
            'member_id' => $request->member_id,
            'member_type' => $model,
        ]);

        return $this->successResponse($groupMember, "تم إضافة العضو للمجموعة بنجاح.");
    }

    public function removeMember(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'member_id' => 'required|integer',
            'member_type' => 'required|string|in:User,Teacher,Admin',
        ]);

        $group = Group::find($request->group_id);
        if (!$group) {
            return $this->errorResponse('المجموعة غير موجودة', 404);
        }

        $model = "App\\Models\\" . $request->member_type;
        if (!class_exists($model)) {
            return $this->errorResponse("نوع العضو غير متاح.", 422);
        }

        $member = $model::find($request->member_id);
        if (!$member) {
            return $this->errorResponse("العضو غير موجود.", 404);
        }

        // Check if the member is in the group
        $exists = GroupMember::where([
            'group_id' => $request->group_id,
            'member_id' => $request->member_id,
            'member_type' => $model,
        ])->exists();
        if (!$exists) {
            return $this->errorResponse("هذا العضو غير موجود داخل المجموعة.", 404);
        }
        // Remove member from the group
        $groupMember = GroupMember::where([
            'group_id' => $request->group_id,
            'member_id' => $request->member_id,
            'member_type' => $model,
        ])->first();
        $groupMember->delete();
        return $this->successResponse(null, "تم حذف العضو من المجموعة بنجاح.");
    }

}
