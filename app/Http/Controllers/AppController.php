<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\SimpleAdminResource;
use App\Http\Resources\SimpleTeacherResource;
use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\UserResource;
use App\Mail\OtpMail;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Number;

class AppController extends Controller
{
    use APIResponse;

    public function allUsers(Request $request)
    {
        // search by name, email, phone, id
        $search = $request->input('search');
        $order_by = $request->input('order_by');
        $order_type = $request->input('order_type', 'asc');

        $query = User::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        if ($order_by && in_array($order_by, ['name', 'email', 'phone', 'id'])) {
            $query->orderBy($order_by, $order_type);
        } else {
            $query->orderBy('id', 'asc');
        }

        $users = $query->paginate(20);

        return $this->successResponse(SimpleUserResource::collection($users));
    }

    public function allAdmins(Request $request)
    {
        // search by name, email, phone, id
        $search = $request->input('search');
        $order_by = $request->input('order_by');
        $order_type = $request->input('order_type', 'asc');

        $query = Admin::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        if ($order_by && in_array($order_by, ['name', 'email', 'phone', 'id'])) {
            $query->orderBy($order_by, $order_type);
        } else {
            $query->orderBy('id', 'asc');
        }

        $admins = $query->paginate(20);

        return $this->successResponse(SimpleAdminResource::collection($admins));
    }

    public function allTeachers(Request $request)
    {
        $search = $request->input('search');
        $order_by = $request->input('order_by');
        $order_type = $request->input('order_type', 'asc');

        $query = Teacher::query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        if ($order_by && in_array($order_by, ['name', 'email', 'phone', 'id'])) {
            $query->orderBy($order_by, $order_type);
        } else {
            $query->orderBy('id', 'asc');
        }

        $teachers = $query->paginate(20);

        return $this->successResponse(SimpleTeacherResource::collection($teachers));
    }
}