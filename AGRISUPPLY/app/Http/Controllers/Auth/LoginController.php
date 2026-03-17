<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username(): string
    {
        return 'email';
    }

    /**
     * Validate the user login request.
     */
    public function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     */
    public function attemptLogin(Request $request)
    {
        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return false;
        }

        // Check if user is inactive
        if ($user->status === 'inactive') {
            return false;
        }

        // Attempt normal login
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $message = 'These credentials do not match our records.';
        
        if ($user && $user->status === 'inactive') {
            $message = 'Your account has been deactivated. Please contact an administrator.';
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => $message,
            ]);
    }
}