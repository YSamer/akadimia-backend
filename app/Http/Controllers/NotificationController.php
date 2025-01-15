<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchApplyResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PaymentResource;
use App\Models\BatchApply;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\APIResponse;
use App\Traits\PushNotification;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use APIResponse, PushNotification;

    public function getNotifications(Request $request)
    {
        $guard = Auth::getDefaultDriver();
        $user = Auth::guard($guard)->user();

        $notifications = $user->allNotifications()->paginate();

        return $this->successResponse(NotificationResource::collection($notifications), 'Notifications fetched successfully.');
    }

    public function readNotification($id)
    {
        $guard = Auth::getDefaultDriver();

        $notification = Auth::guard($guard)->user()->allNotifications()->where('id', $id)->first();
        if (!$notification) {
            return $this->errorResponse('Not found', 404);
        }
        $notification->markAsRead();

        return $this->successResponse(new NotificationResource($notification), 'الإشعار مقروء.');
    }

    public function markAllAsRead()
    {
        $guard = Auth::getDefaultDriver();
        Auth::guard($guard)->user()->unreadedNotifications()->markAsRead();

        return $this->successResponse([], 'All notifications marked as read successfully.');
    }


    public function createNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_type' => 'required|in:user,teacher,admin',
            'user_id' => 'required|exists:' . $request->user_type . 's' . ',id'
        ]);

        $userType = 'App\\Models\\' . ucfirst($request->user_type);

        $notification = $this->create(
            $request->title,
            $request->body,
            $request->user_id,
            $userType,
        );

        return $this->successResponse(new NotificationResource($notification), 'تم إنشاء الإشعار.');
    }

    public function deleteNotification(Request $request, $id)
    {
        $guard = Auth::getDefaultDriver();
        $notification = Auth::guard($guard)->user()->allNotifications()->where('id', $id)->first();
        if (!$notification) {
            return $this->errorResponse('Not found', 404);
        }
        $notification->delete();

        return $this->successResponse([], 'تم حذف الإشعار.');
    }

    public function create($title, $body, $user_id, $user_type)
    {
        // $userType = 'App\\Models\\' . ucfirst(string: $user_type);

        $notification = new Notification();
        $notification->title = $title;
        $notification->body = $body;
        $notification->user_type = $user_type;
        $notification->user_id = $user_id;
        $notification->save();

        $this->pushNotification($notification);

        return $notification;
    }
}