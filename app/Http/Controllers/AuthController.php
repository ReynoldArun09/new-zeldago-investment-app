<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the user login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the user login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect to user dashboard
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the user register form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle the user registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'sponsor_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $sponsorId = null;
        if (!empty($validated['sponsor_id'])) {
            $sponsor = User::where('referral_code', $validated['sponsor_id'])
                            ->orWhere('username', $validated['sponsor_id'])
                            ->first();

            if (!$sponsor) {
                return back()->withErrors(['sponsor_id' => 'Invalid Referral Code.'])->withInput();
            }
            if (!$sponsor->is_active) {
                return back()->withErrors(['sponsor_id' => 'Sponsor is inactive.'])->withInput();
            }

            $sponsorId = $sponsor->id;
        }

        $user = User::create([
            'sponsor_id' => $sponsorId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('web')->login($user);

        return redirect('/dashboard');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
