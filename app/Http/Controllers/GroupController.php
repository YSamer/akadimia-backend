<?php

namespace App\Http\Controllers;
use App\Http\Resources\GroupResource;
use App\Models\Group;
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

    public function show($id)
    {
        $group = Group::with('batch')->find($id);
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
            return $this->errorResponse('المجموعة ��ير موجودة', 404);
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
}
