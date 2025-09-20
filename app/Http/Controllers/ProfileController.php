<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);
        return view('profile.show', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user = Auth::user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $profile->name = $request->name;
        if ($request->avatar) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->back()->with('success', 'Profile updated!');
    }

    // Logout from all devices
    public function logoutAllDevices()
    {
        Auth::logoutOtherDevices(request('password'));
        return redirect()->route('dashboard')->with('success', 'Logged out from all devices.');
    }
}
