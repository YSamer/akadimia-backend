<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Teacher;
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

class TeacherAuthController extends Controller
{
    use APIResponse, PushNotification;

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:teachers,email',
            'phone' => 'required|string|max:15|unique:teachers,phone',
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

            $teacher = Teacher::create([
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

            cache()->put("teacher_otp_{$teacher->id}", $otp, 300);

            Mail::to($teacher->email)->send(new OtpMail($otp, $teacher));

            DB::commit();

            return $this->successResponse($teacher, 'تم إنشاء الحساب بنجاح، برجاء تفعيل الإيميل.');
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

        $teacher = Teacher::where('email', $request->email)->first();

        $storedOtp = cache()->get("teacher_otp_{$teacher->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $teacher->markEmailAsVerified();
            cache()->forget("teacher_otp_{$teacher->id}");
            $token = $teacher->createToken('API Token')->plainTextToken;

            return $this->successResponse(['token' => $token, 'teacher' => $teacher], 'Email verified successfully.');
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

        // Find the teacher by email
        $teacher = Auth::guard('teacher')->getProvider()->retrieveByCredentials(['email' => $request->email]);

        // Check if the teacher exists and the password is correct
        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            return $this->errorResponse('بيانات الدخول غير صحيحة', null, 401);
        }

        // Check if the teacher's email is verified
        if (!$teacher->hasVerifiedEmail()) {
            cache()->forget("teacher_otp_{$teacher->id}");
            $otp = rand(100000, 999999);
            cache()->put("teacher_otp_{$teacher->id}", $otp, 300);
            Mail::to($teacher->email)->send(new OtpMail($otp, $teacher));
            return $this->successResponse(['teacher' => $teacher], 'برجاء تفعيل الحساب.');
        }

        $teacher->device_token = $request->device_token;
        $teacher->save();
        $token = $teacher->createToken('API Token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'teacher' => $teacher,
        ], 'تم تسجيل الدخول بنجاح');
    }

    public function reSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:teachers,email',
            'forget' => 'nullable|boolean',
        ]);

        $user = Teacher::where('email', $request->email)->first();
        if ($request->forget) {
            cache()->forget("teacher_otp_reset_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("teacher_otp_reset_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } else {
            cache()->forget("teacher_otp_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("teacher_otp_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        }

        return $this->successResponse(null, 'OTP re-sent to your email.');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:teachers,email'
        ]);

        $teacher = Teacher::where('email', $request->email)->first();

        $otp = rand(100000, 999999);

        cache()->put("teacher_otp_reset_{$teacher->id}", $otp, 300);
        Mail::to($teacher->email)->send(new OtpMail($otp, $teacher));

        return $this->successResponse(null, 'OTP sent to your email for password reset.');
    }

    public function verifyOtpForPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|size:6',
        ]);

        $teacher = Teacher::where('email', $request->email)->first();

        $storedOtp = cache()->get("teacher_otp_reset_{$teacher->id}");

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

        $teacher = Teacher::where('email', $request->email)->first();

        $storedOtp = cache()->get("teacher_otp_reset_{$teacher->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $teacher->password = Hash::make($request->password);
            $teacher->save();

            cache()->forget("teacher_otp_reset_{$teacher->id}");

            return $this->successResponse(null, 'Password has been reset successfully.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function autoLogin(Request $request)
    {
        $teacher = $request->user('teacher');
        if (!$teacher) {
            return $this->errorResponse('Teacher not found', null, 404);
        }

        if (!$teacher->hasVerifiedEmail()) {
            return $this->errorResponse('برجاء تفعيل الحساب.', null, 403);
        }

        return $this->successResponse(['teacher' => $teacher], 'Auto تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request)
    {
        $request->user('teacher')->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function profile(Request $request)
    {
        $teacher = $request->user('teacher');
        if (!$teacher) {
            return $this->errorResponse('Teacher not found', null, 404);
        }

        if (!$teacher->hasVerifiedEmail()) {
            return $this->errorResponse('برجاء تفعيل الحساب.', null, 403);
        }

        return $this->successResponse(['teacher' => $teacher], 'Auto تم تسجيل الدخول بنجاح');
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'old_password' => 'sometimes|string|min:8',
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:15|unique:teachers,phone',
            'telegram' => 'sometimes|string|max:255|unique:admins,telegram',
            'birth_date' => 'sometimes|date',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $teacher = $request->user('teacher');

        if ($request->has('name'))
            $teacher->name = $request->name;
        if ($request->has('phone'))
            $teacher->phone = $request->phone;
        if ($request->has('telegram'))
            $teacher->telegram = $request->telegram;
        if ($request->has('birth_date'))
            $teacher->birth_date = $request->birth_date;
        if ($request->has('old_password')) {
            if (Hash::check($request->old_password, $teacher->password)) {
                $teacher->password = Hash::make($request->password);
            } else {
                return $this->errorResponse('كلمة المرور القديمة غير صحيحة', null, 400);
            }
        }
        if ($request->hasFile('image')) {
            if ($teacher->image) {
                Storage::delete($teacher->image);
            }

            $path = $request->file('image')->store('uploads/profile');
            $teacher->image = $path;
        }

        $teacher->save();

        return $this->successResponse($teacher, 'تم تحديث البيانات بنجاح');
    }


    public function notify(Teacher $teacher)
    {
        return response()->json($this->sendNotification(
            $teacher->device_token,
            'تم تحديث البيانات بنجاح',
            'edit',
            ['type' => 'test']
        ));
    }
}
