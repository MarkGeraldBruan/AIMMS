<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // DO NOT use SendsPasswordResetEmails trait - it will override our custom methods
    
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset code to user's email
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We couldn\'t find an account with that email address.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // Send email with code
        try {
            Mail::to($user->email)->send(new PasswordResetCodeMail($user, $code));
            
            return redirect()->route('password.verify.form')
                ->with('email', $request->email)
                ->with('success', 'A verification code has been sent to your email!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send verification code. Please try again.');
        }
    }

    /**
     * Show verify code form
     */
    public function showVerifyForm()
    {
        if (!session('email')) {
            return redirect()->route('password.request');
        }
        
        return view('auth.verify-code');
    }

    /**
     * Verify the reset code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->with('error', 'Invalid or expired verification code.');
        }

        // Check if code is expired (15 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Verification code has expired. Please request a new one.');
        }

        // Verify code
        $codes = [$request->code];
        $isValid = false;
        
        foreach ($codes as $code) {
            if (Hash::check($code, $resetRecord->token)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            return back()->with('error', 'Invalid verification code.');
        }

        // Code is valid, redirect to reset password form
        return redirect()->route('password.reset.form')
            ->with('email', $request->email)
            ->with('verified', true);
    }

    /**
     * Show reset password form
     */
    public function showResetForm()
    {
        if (!session('email') || !session('verified')) {
            return redirect()->route('password.request');
        }
        
        return view('auth.reset-password');
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully! You can now login with your new password.');
    }

    /**
     * Resend verification code
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate new 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update code in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // Send email with new code
        try {
            Mail::to($user->email)->send(new PasswordResetCodeMail($user, $code));
            
            return back()->with('success', 'A new verification code has been sent to your email!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send verification code. Please try again.');
        }
    }
}