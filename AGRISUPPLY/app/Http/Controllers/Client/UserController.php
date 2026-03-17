<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\UserCredentialMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admins can view users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = User::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(15);

        return view('client.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only admins can create users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('client.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admins can create users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'can_create' => 'boolean',
            'can_read' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
            'can_stock_in' => 'boolean',
            'can_stock_out' => 'boolean',
        ]);

        // Use the entered password
        $plainPassword = $request->password;

        $validated['password'] = Hash::make($plainPassword);
        $validated['status'] = 'active';

        $user = User::create($validated);

        // Send email with credentials
        try {
            Mail::to($user->email)->send(new UserCredentialMail($user, $plainPassword));
            \Log::info('User credentials email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            // Log the error but don't fail the user creation
            \Log::error('Failed to send user credentials email to ' . $user->email . ': ' . $e->getMessage());
            // You can optionally add a flash message here if needed
            // session()->flash('warning', 'User created but email could not be sent. Please check email configuration.');
        }

        return redirect()->route('users.index')->with('success', 'User created successfully! Credentials sent to email.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Only admins can view user details
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('client.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Only admins can edit users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Prevent editing self
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot edit your own account.');
        }

        return view('client.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Only admins can update users
        if (!auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied. Admin privileges required.'], 403);
            }
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Prevent updating self
        if ($user->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You cannot edit your own account.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'You cannot edit your own account.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',
            'can_create' => 'boolean',
            'can_read' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
            'can_stock_in' => 'boolean',
            'can_stock_out' => 'boolean',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'User updated successfully!']);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Only admins can delete users
        if (!auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied. Admin privileges required.'], 403);
            }
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You cannot delete your own account.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting other admins if current user is not admin
        if ($user->isAdmin() && !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            abort(403, 'Access denied.');
        }

        try {
            $user->delete();

            if ($request->expectsJson()) {
                return response()->json(['success' => 'User deleted successfully!']);
            }

            return redirect()->route('users.index')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete user: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to delete user. Please try again.'], 500);
            }

            return redirect()->route('users.index')->with('error', 'Failed to delete user. Please try again.');
        }
    }
}
