<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Admin;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Number;

class AdminAuthController extends Controller
{
    use APIResponse;

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:admins,email',
            'phone' => 'required|string|max:15|unique:admins,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
            ]);

            $otp = rand(100000, 999999);

            cache()->put("admin_otp_{$admin->id}", $otp, 300);

            Mail::to($admin->email)->send(new OtpMail($otp, $admin));

            DB::commit();

            return $this->successResponse($admin, 'Registration successful. Please verify your email.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->errorResponse('Registration failed. Please try again.' . $e->getMessage(), 500);
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
        ]);

        // Find the admin by email
        $admin = Auth::guard('admin')->getProvider()->retrieveByCredentials(['email' => $request->email]);

        // Check if the admin exists and the password is correct
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        // Check if the admin's email is verified
        if (!$admin->hasVerifiedEmail()) {
            return $this->errorResponse('Please verify your email.', null, 403);
        }

        // Generate a Sanctum token
        $token = $admin->createToken('API Token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'admin' => $admin,
        ], 'Login successful');
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
            return $this->errorResponse('Please verify your email.', null, 403);
        }

        return $this->successResponse(['admin' => $admin], 'Auto login successful');
    }

    public function logout(Request $request)
    {
        $request->user('admin')->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:15|unique:admins,phone',
            // 'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date',

        ]);

        $admin = $request->user('admin');

        if ($request->has('name'))
            $admin->name = $request->name;
        // if ($request->has('email'))
        //     $admin->email = $request->email;
        if ($request->has('phone'))
            $admin->phone = $request->phone;
        // if ($request->has('gender'))
        //     $admin->gender = $request->gender;
        if ($request->has('birth_date'))
            $admin->birth_date = $request->birth_date;
        if ($request->has('password'))
            $admin->password = Hash::make($request->password);

        $admin->save();

        return $this->successResponse($admin, 'Profile updated successfully');
    }
}
