<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\Otp;
use App\Models\Student;
use App\Models\User;
use App\Models\Major;
use App\Models\StudyPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailMail;
use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get registration metadata (majors and available years).
     */
    public function metadata(): JsonResponse
    {
        $majors = Major::select('id', 'name')->get();
        $years = StudyPlan::select('effective_year')
            ->distinct()
            ->orderBy('effective_year', 'desc')
            ->pluck('effective_year');

        return response()->json([
            'majors' => $majors,
            'years' => $years,
        ]);
    }

    /**
     * Register a new user and student profile.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Auto-assignment of study plan
        $appropriatePlan = StudyPlan::where('major_id', $request->major_id)
            ->where('effective_year', '<=', $request->enrollment_year)
            ->orderBy('effective_year', 'desc')
            ->first();

        if (!$appropriatePlan) {
            return response()->json([
                'message' => 'No study plan found for this major and enrollment year.'
            ], 422);
        }

        $userData = DB::transaction(function () use ($request, $appropriatePlan) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => 'user_' . Str::random(8),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birth_date' => $request->birth_date,
                'role' => 'student',
            ]);

            $user->student()->create([
                'major_id' => $request->major_id,
                'study_plan_id' => $appropriatePlan->id,
                'enrollment_year' => $request->enrollment_year,
            ]);

            return $user;
        });

        // Generate OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::updateOrCreate(
            ['email' => $userData->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        // Send OTP Email
        // Send Verification Email
        Mail::to($userData->email)->send(new VerifyEmailMail($code, $userData->first_name));

        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'user' => new UserResource($userData->load('student')),
            'otp' => config('app.env') === 'local' ? $code : null,
        ], 201);
    }

    /**
     * Authenticate user and send OTP.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('aqran_auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user->load('student')),
            'token' => $token,
        ]);
    }

    /**
     * Verify OTP and issue Sanctum token.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = Otp::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP code.',
            ], 422);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Delete used OTP
        $otp->delete();

        $token = $user->createToken('aqran_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user->load('student')),
            'token' => $token,
        ]);
    }

    /**
     * Send OTP for password reset.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Otp::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $user = User::where('email', $request->email)->first();
        Mail::to($request->email)->send(new ResetPasswordMail($code, $user->first_name));

        return response()->json([
            'message' => 'Reset code sent successfully.',
            'otp' => config('app.env') === 'local' ? $code : null,
        ]);
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Otp::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $user = User::where('email', $request->email)->first();
        // Default to VerifyEmailMail for resend as it's the primary registration flow
        Mail::to($request->email)->send(new VerifyEmailMail($code, $user?->first_name ?? 'User'));

        return response()->json([
            'message' => 'OTP resent successfully.',
            'otp' => config('app.env') === 'local' ? $code : null,
        ]);
    }

    /**
     * Reset user password using OTP.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP code.',
            ], 422);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Update password with Hash::make
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used OTP
        $otp->delete();

        return response()->json([
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }

    /**
     * Validate OTP without deleting it (for reset password flow).
     */
    public function validateOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = Otp::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP code.',
            ], 422);
        }

        return response()->json([
            'message' => 'OTP is valid.',
        ]);
    }

    /**
     * Revoke the current user's token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
