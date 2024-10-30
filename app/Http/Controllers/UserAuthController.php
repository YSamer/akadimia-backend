<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserAuthController extends Controller
{
    use APIResponse;

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
        ]);
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
            ]);

            // Generate OTP
            $otp = rand(100000, 999999);

            cache()->put("otp_{$user->id}", $otp, 300);

            // Send OTP to user (can be sent via email or SMS)
            Mail::to($user->email)->send(new OtpMail($otp, $user));

            // Send verification email
            // $user->sendEmailVerificationNotification();
            DB::commit();

            return $this->successResponse($user, 'Registration successful. Please verify your email.');
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

        $user = User::where('email', $request->email)->first();

        $storedOtp = cache()->get("otp_{$user->id}");

        if ($storedOtp && hash_equals((string) $request->otp, (string) $storedOtp)) {
            $user->markEmailAsVerified();
            cache()->forget("otp_{$user->id}");

            return $this->successResponse(null, 'Email verified successfully.');
        }

        return $this->errorResponse('Invalid or expired OTP.', null, 400);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Please verify your email.', null, 403);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return $this->successResponse(['token' => $token, 'user' => $user], 'Login successful');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

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

        return $this->successResponse(['user' => $user], 'Auto login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:15|unique:users,phone',
            // 'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date',

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
        if ($request->has('password'))
            $user->password = Hash::make($request->password);

        $user->save();

        return $this->successResponse($user, 'Profile updated successfully');
    }
}
