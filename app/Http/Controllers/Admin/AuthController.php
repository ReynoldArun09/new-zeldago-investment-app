<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.settings.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'avatar' => ['nullable', 'image', 'max:2048']
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $admin->avatar_url = '/storage/' . $path;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function password()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.settings.password', compact('admin'));
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'oldPassword' => ['required'],
            'newPassword' => ['required', 'string', 'min:6'],
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->oldPassword, $admin->password)) {
            return redirect()->back()->withErrors(['oldPassword' => 'The current password does not match.']);
        }

        $admin->password = \Illuminate\Support\Facades\Hash::make($request->newPassword);
        $admin->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function showSettingsUnlockForm()
    {
        return view('admin.settings.unlock');
    }

    public function unlockSettings(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'password' => ['required'],
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $admin->password)) {
            return redirect()->back()->withErrors(['password' => 'The provided password does not match our records.']);
        }

        $request->session()->put('admin_settings_unlocked_at', time());

        $intendedUrl = $request->session()->pull('url.intended', route('admin.settings.admin'));
        return redirect()->to($intendedUrl);
    }
}
