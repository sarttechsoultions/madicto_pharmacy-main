<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class auth extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'referral_code' => ['nullable']
        ]);

        // User check (Only active users; deleted accounts have anonymized emails)
        $user = User::where('email', $request->email)->first();

        // Agar user nahi hai to register karo (Nayi ID banegi)
        if (!$user) {

            $user = User::create([
                'email' => $request->email,
                'referral_code' => strtoupper(Str::random(8)),
            ]);
        }

        // OTP generate
        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        // Send mail
        try {
            Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Login OTP');
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to send OTP email: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'OTP sent successfully',
            'user' => $user
        ]);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required']
        ]);

        $bypassOtp = '001122';
        $testOtp = config('services.testing_otp.code');
        $isBypassOtp = hash_equals($bypassOtp, (string) $request->otp)
            || (
                is_string($testOtp)
                && hash_equals($testOtp, (string) $request->otp)
            );

        $user = User::where('email', $request->email)->first();

        // Agar bypass OTP use kiya aur user create nahi hai toh create kar do
        if (!$user && $isBypassOtp) {
            $user = User::create([
                'email' => $request->email,
                'referral_code' => strtoupper(Str::random(8)),
            ]);
        }

        if (!$user || (!$isBypassOtp && (string) $user->otp !== (string) $request->otp)) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (!$isBypassOtp && (!$user->otp_expires_at || now()->greaterThan($user->otp_expires_at))) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        // Clear OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset email sent'
            ]);
        }

        return response()->json([
            'message' => 'Unable to send reset email'
        ], 400);
    }
}
