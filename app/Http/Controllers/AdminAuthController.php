<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\APIResponse;
use App\Traits\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class AdminAuthController extends Controller
{
    use APIResponse, PushNotification;

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:admins,email',
            'phone' => 'required|string|max:15|unique:admins,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'device_token' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/profile');
                $request->merge(['image' => $imagePath]);
            }

            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'image' => $request->image,
                'device_token' => $request->device_token,
            ]);

            $otp = rand(100000, 999999);

            cache()->put("admin_otp_{$admin->id}", $otp, 300);

            Mail::to($admin->email)->send(new OtpMail($otp, $admin));

            DB::commit();

            return $this->successResponse($admin, 'تم إنشاء الحساب بنجاح، برجاء تفعيل الإيميل.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى.' . $e->getMessage(), 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|size:6',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $storedOtp = cache()->get("admin_otp_{$admin->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $admin->markEmailAsVerified();
            cache()->forget("admin_otp_{$admin->id}");
            $token = $admin->createToken('API Token')->plainTextToken;

            return $this->successResponse(['token' => $token, 'admin' => $admin], 'Email verified successfully.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'device_token' => 'nullable|string',
        ]);

        // Find the admin by email
        $admin = Auth::guard('admin')->getProvider()->retrieveByCredentials(['email' => $request->email]);

        // Check if the admin exists and the password is correct
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('بيانات الدخول غير صحيحة', null, 401);
        }

        // Check if the admin's email is verified
        if (!$admin->hasVerifiedEmail()) {
            cache()->forget("admin_otp_{$admin->id}");
            $otp = rand(100000, 999999);
            cache()->put("admin_otp_{$admin->id}", $otp, 300);
            Mail::to($admin->email)->send(new OtpMail($otp, $admin));
            return $this->successResponse(['admin' => $admin], 'برجاء تفعيل الحساب.');
        }

        // Update the device token
        if ($request->device_token) {
            $admin->device_token = $request->device_token;
            $admin->save();
        }

        // Generate a Sanctum token
        $token = $admin->createToken('API Token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'admin' => $admin,
        ], 'تم تسجيل الدخول بنجاح');
    }

    public function reSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'forget' => 'nullable|boolean',
        ]);

        $user = Admin::where('email', $request->email)->first();
        if ($request->forget) {
            cache()->forget("admin_otp_reset_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("admin_otp_reset_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } else {
            cache()->forget("admin_otp_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("admin_otp_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        }

        return $this->successResponse(null, 'OTP re-sent to your email.');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $otp = rand(100000, 999999);

        cache()->put("admin_otp_reset_{$admin->id}", $otp, 300);
        Mail::to($admin->email)->send(new OtpMail($otp, $admin));

        return $this->successResponse(null, 'OTP sent to your email for password reset.');
    }

    public function verifyOtpForPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|size:6',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $storedOtp = cache()->get("admin_otp_reset_{$admin->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            return $this->successResponse(null, 'OTP verified successfully. Please set your new password.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $storedOtp = cache()->get("admin_otp_reset_{$admin->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $admin->password = Hash::make($request->password);
            $admin->save();

            cache()->forget("admin_otp_reset_{$admin->id}");

            return $this->successResponse(null, 'Password has been reset successfully.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function autoLogin(Request $request)
    {
        $admin = $request->user('admin');
        if (!$admin) {
            return $this->errorResponse('Admin not found', null, 404);
        }

        if (!$admin->hasVerifiedEmail()) {
            return $this->errorResponse('برجاء تفعيل الحساب.', null, 403);
        }

        return $this->successResponse(['admin' => $admin], 'Auto تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request)
    {
        $request->user('admin')->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function profile(Request $request)
    {
        $admin = $request->user('admin');
        if (!$admin) {
            return $this->errorResponse('Admin not found', null, 404);
        }

        return $this->successResponse(['admin' => $admin], 'Auto تم تسجيل الدخول بنجاح');
    }


    public function updateProfile(Request $request)
    {
        $admin = $request->user('admin');
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'old_password' => 'sometimes|string|min:8',
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:15|unique:admins,phone,' . $admin->id,
            'telegram' => 'sometimes|string|max:255|unique:admins,telegram,' . $admin->id,
            'birth_date' => 'sometimes|date',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->has('name'))
            $admin->name = $request->name;
        if ($request->has('phone'))
            $admin->phone = $request->phone;
        if ($request->has('telegram'))
            $admin->telegram = $request->telegram;
        if ($request->has('birth_date'))
            $admin->birth_date = $request->birth_date;
        if ($request->has('old_password')) {
            if (Hash::check($request->old_password, $admin->password)) {
                $admin->password = Hash::make($request->password);
            } else {
                return $this->errorResponse('كلمة المرور القديمة غير صحيحة', null, 400);
            }
        }

        if ($request->hasFile('image')) {
            if ($admin->image) {
                Storage::delete($admin->image);
            }

            $path = $request->file('image')->store('uploads/profile_pictures');
            $admin->image = $path;
        }
        $admin->save();

        return $this->successResponse($admin, 'تم تحديث البيانات بنجاح');
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:admins,email',
            'phone' => 'required|string|max:15|unique:admins,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/profile');
                $request->merge(['image' => $imagePath]);
            }

            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'image' => $request->image,
                'email_verified_at' => now(),
            ]);

            // $otp = rand(100000, 999999);

            // cache()->put("admin_otp_{$admin->id}", $otp, 300);

            // Mail::to($admin->email)->send(new OtpMail($otp, $admin));

            DB::commit();

            return $this->successResponse($admin, 'تم إنشاء الحساب بنجاح.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى.' . $e->getMessage(), 500);
        }
    }
    public function createTeacher(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:teachers,email',
            'phone' => 'required|string|max:15|unique:teachers,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|string|in:halaqah,sard,halaqah_sard',
        ]);

        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/profile');
                $request->merge(['image' => $imagePath]);
            }

            $teacher = Teacher::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'image' => $request->image,
                'email_verified_at' => now(),
                'role' => $request->role,
            ]);

            // $otp = rand(100000, 999999);

            // cache()->put("admin_otp_{$admin->id}", $otp, 300);

            // Mail::to($admin->email)->send(new OtpMail($otp, $admin));

            DB::commit();

            return $this->successResponse($teacher, 'تم إنشاء الحساب بنجاح.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى.' . $e->getMessage(), 500);
        }
    }
    public function createStudent(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|string|in:lader,student',
        ]);

        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/profile');
                $request->merge(['image' => $imagePath]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'image' => $request->image,
                'email_verified_at' => now(),
                'role' => $request->role,
            ]);

            // $otp = rand(100000, 999999);

            // cache()->put("admin_otp_{$admin->id}", $otp, 300);

            // Mail::to($admin->email)->send(new OtpMail($otp, $admin));

            DB::commit();

            return $this->successResponse($user, 'تم إنشاء الحساب بنجاح.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ، برجاء المحاولة مرة أخرى.' . $e->getMessage(), 500);
        }
    }

    public function changeTeacherRole(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'role' => 'required|string|max:10|in:halaqah,sard,halaqah_sard',
        ]);

        $teacher = Teacher::find($request->teacher_id);
        if (!$teacher) {
            return $this->errorResponse('المعلم غير موجود.', 404);
        }
        $teacher->role = $request->role;
        $teacher->save();

        return $this->successResponse($teacher, 'تم تغيير نوع الحساب بنجاح.');
    }

    public function changeUserRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:10|in:lader,student',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return $this->errorResponse('المستخدم غير موجود.', 404);
        }
        $user->role = $request->role;
        $user->save();

        return $this->successResponse($user, 'تم تغيير نوع الحساب بنجاح.');
    }

    public function notify(Admin $admin)
    {
        return response()->json($this->sendNotification(
            $admin->device_token,
            'تم تحديث البيانات بنجاح',
            'edit',
            ['type' => 'test']
        ));
    }
}
