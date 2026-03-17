<?php

// FILE 1: app/Http/Middleware/CheckUserStatus.php (CREATE NEW FILE)

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user is inactive, logout immediately
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')
                    ->with('error', 'Your account has been deactivated. Please contact an administrator.');
            }
        }
        
        return $next($request);
    }
}

// FILE 2: app/Http/Controllers/Auth/LoginController.php (UPDATE)

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Override credentials to check if user is active
     */
    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['status' => 'active'] // Only allow login if status is 'active'
        );
    }

    /**
     * Handle a failed login attempt
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'These credentials do not match our records. Your account may be inactive.',
            ]);
    }
}

// FILE 3: bootstrap/app.php (UPDATE - for Laravel 11+)
// OR config/http.php (for Laravel 10)

// For Laravel 11+, in bootstrap/app.php, find the ->withMiddleware() section and add:

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// For Laravel 10, in app/Http/Kernel.php, add to the $middlewareGroups array:

protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\CheckUserStatus::class,
    ],
];

// FILE 4: app/Http/Controllers/Client/UserController.php (UPDATE - the update method)

public function update(UserRequest $request, string $id)
{
    $validated = $request->validated();
    $user = User::findOrFail($id);
    
    // Prevent admin from deactivating their own account
    if ($validated['status'] === 'inactive' && $user->id === auth()->user()->id) {
        return redirect()->back()
            ->withErrors(['status' => 'You cannot deactivate your own account.'])
            ->withInput();
    }
    
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];
    $user->status = $validated['status'];
    
    // If admin role, set all permissions to true
    if ($validated['role'] === 'admin') {
        $user->can_create = true;
        $user->can_read = true;
        $user->can_update = true;
        $user->can_delete = true;
    } else {
        $user->can_create = $request->boolean('can_create', false);
        $user->can_read = $request->boolean('can_read', true);
        $user->can_update = $request->boolean('can_update', false);
        $user->can_delete = $request->boolean('can_delete', false);
    }
    
    // Only update password if provided
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }
    
    $user->save();

    $message = 'User information has been updated successfully!';
    if ($user->status === 'inactive') {
        $message = 'User has been deactivated and cannot log in.';
    }

    return redirect()->route('users.index')->with('success', $message);
}