<?php

namespace App\Http\Controllers;

use App\Http\Resources\SimpleAdminResource;
use App\Http\Resources\SimpleItemResource;
use App\Http\Resources\SimpleTeacherResource;
use App\Http\Resources\SimpleUserResource;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class AppController extends Controller
{
    use APIResponse;

    public function allUsers(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 25) : 0;
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = User::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        $query->orderBy($sortBy, $orderBy);

        $users = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );

        return $this->successResponse(SimpleUserResource::collection($users)->response()->getData());
    }

    public function allGroupUsers(Request $request, $groupId)
    {
        $users = User::whereHas('groups', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->get();

        return $this->successResponse(SimpleItemResource::collection($users));
    }

    public function allAdmins(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 25) : 0;
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = Admin::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        $query->orderBy($sortBy, $orderBy);

        $admins = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );

        return $this->successResponse(SimpleAdminResource::collection($admins)->response()->getData());
    }

    public function allTeachers(Request $request)
    {
        $perPage = $request->per_page > 0 ? $request->input('per_page', 25) : 0;
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $orderBy = $request->input('order_by', 'asc');

        $query = Teacher::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        $query->orderBy($sortBy, $orderBy);

        $teachers = $query->paginate(
            function ($total) use ($perPage) {
                return $perPage == -1 ? $total : $perPage;
            }
        );

        return $this->successResponse(SimpleTeacherResource::collection($teachers)->response()->getData());
    }
}