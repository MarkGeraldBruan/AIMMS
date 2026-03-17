<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index()
    {
        $user = Auth::user();
        return view('client.profile.index', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'avatar' => [
                'nullable',
                File::image()
                    ->max(2048) // 2MB max
            ],
            'logo' => [
                'nullable',
                File::image()
                    ->max(2048) // 2MB max
            ]
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Handle logo upload (admin only)
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo && Storage::disk('public')->exists($user->logo)) {
                Storage::disk('public')->delete($user->logo);
            }
            
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $user->logo = $logoPath;
        }

        // Update basic info
        $user->name = $request->name;

        // Update password if provided
        if ($request->filled('password') && $request->filled('current_password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove user's avatar
     */
    public function removeAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->avatar = null;
        $user->save();
        
        return response()->json(['success' => true, 'message' => 'Avatar removed successfully']);
    }

    /**
     * Remove user's logo (admin only)
     */
    public function removeLogo()
    {
        $user = Auth::user();
        
        // Only admin can remove logo
        if (!$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($user->logo && Storage::disk('public')->exists($user->logo)) {
            Storage::disk('public')->delete($user->logo);
        }
        
        $user->logo = null;
        $user->save();
        
        return response()->json(['success' => true, 'message' => 'Logo removed successfully']);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}