<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function show()
    {
        return view('users.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:100',
            'social_links' => 'nullable|array',
            'bio' => 'nullable|string|max:1000',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Process and store new avatar
                $image = $request->file('avatar');
                $filename = 'avatars/' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();

                $img = Image::make($image)->fit(300, 300)->encode();
                Storage::disk('public')->put($filename, $img);

                $validated['avatar'] = $filename;
            }

            // Update password if provided
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('users.profile')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        try {
            Auth::logoutOtherDevices($request->password);
            return redirect()->route('dashboard')
                ->with('success', 'Successfully logged out from all other devices.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to logout from other devices: ' . $e->getMessage());
        }
    }
}
