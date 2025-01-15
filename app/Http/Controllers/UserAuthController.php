<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\APIResponse;
use App\Traits\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
    use APIResponse, PushNotification;

    public function register(Request $request)
    {
        // Check if email already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return $this->errorResponse('Email already exists. Please login.');
        }

        // Validate request data
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
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

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'image' => $request->image,
                'device_token' => $request->device_token,
            ]);

            cache()->forget("otp_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("otp_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));

            DB::commit();

            return $this->successResponse($user, 'تم إنشاء الحساب بنجاح، برجاء تفعيل الإيميل.');
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

        $user = User::where('email', $request->email)->first();

        $storedOtp = cache()->get("otp_{$user->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $user->markEmailAsVerified();
            cache()->forget("otp_{$user->id}");

            $token = $user->createToken('API Token')->plainTextToken;

            return $this->successResponse(['token' => $token, 'user' => $user], 'Email verified successful');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function reSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'forget' => 'nullable|boolean',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($request->forget) {
            cache()->forget("otp_reset_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("otp_reset_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } else {
            cache()->forget("otp_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("otp_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        }

        return $this->successResponse(null, 'OTP re-sent to your email.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'device_token' => 'nullable|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            cache()->forget("otp_{$user->id}");
            $otp = rand(100000, 999999);
            cache()->put("otp_{$user->id}", $otp, 300);
            Mail::to($user->email)->send(new OtpMail($otp, $user));
            return $this->successResponse(['user' => $user], 'تم تسجيل الدخول بنجاح');
        }

        $user->device_token = $request->device_token;
        $user->save();
        $token = $user->createToken('API Token')->plainTextToken;

        return $this->successResponse(['token' => $token, 'user' => $user], 'تم تسجيل الدخول بنجاح');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        cache()->forget("otp_reset_{$user->id}");
        $otp = rand(100000, 999999);
        cache()->put("otp_reset_{$user->id}", $otp, 300);
        Mail::to($user->email)->send(new OtpMail($otp, $user));

        return $this->successResponse(null, 'OTP sent to your email for password reset.');
    }

    public function verifyOtpForPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        $storedOtp = cache()->get("otp_reset_{$user->id}");

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

        $user = User::where('email', $request->email)->first();

        $storedOtp = cache()->get("otp_reset_{$user->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $user->password = Hash::make($request->password);
            $user->save();

            cache()->forget("otp_reset_{$user->id}");

            return $this->successResponse(null, 'Password has been reset successfully.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function autoLogin(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Please verify your email.', null, 403);
        }

        return $this->successResponse(['user' => $user], 'Auto تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        return $this->successResponse(['user' => $user], 'Auto تم تسجيل الدخول بنجاح');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'old_password' => 'sometimes|string|min:8',
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:15|unique:users,phone',
            // 'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();

        if ($request->has('name'))
            $user->name = $request->name;
        // if ($request->has('email'))
        //     $user->email = $request->email;
        if ($request->has('phone'))
            $user->phone = $request->phone;
        // if ($request->has('gender'))
        //     $user->gender = $request->gender;
        if ($request->has('birth_date'))
            $user->birth_date = $request->birth_date;
        if ($request->has('old_password') && Hash::check($request->old_password, $user->password)) {
            if ($request->has('password'))
                $user->password = Hash::make($request->password);
        } else {
            return $this->errorResponse('كلمة المرور القديمة غير صحيحة', null, 400);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::delete($user->image);
            }

            $path = $request->file('image')->store('uploads/profile_pictures');
            $user->image = $path;
        }

        $user->save();

        return $this->successResponse($user, 'Profile updated successfully');
    }

    public function notify(User $user)
    {
        return response()->json($this->sendNotification(
            $user->device_token,
            'Profile updated successfully',
            'edit',
            ['type' => 'test'],
        ));
    }
}
