<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NetworkController extends Controller
{
    public function referrals()
    {
        $user = Auth::user();
        
        // Load the sponsor and direct referrals
        $user->load(['sponsor', 'directReferrals']);

        return view('user.network.referrals', compact('user'));
    }

    public function genealogy()
    {
        $user = Auth::user();
        
        // Eager load downline for the tree
        $user->load('downline');

        return view('user.network.genealogy', compact('user'));
    }

    public function addInvestor(Request $request)
    {
        if (Auth::user()->account_type !== 'Agent') {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $sponsor = Auth::user();

        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->phone = $request->phone;
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->account_type = 'Normal User';
        $user->sponsor_id = $sponsor->id;
        $user->save();

        return back()->with('success', 'Investor added successfully.');
    }

    public function investorInvestments($id)
    {
        $sponsor = Auth::user();
        $investor = \App\Models\User::where('id', $id)->where('sponsor_id', $sponsor->id)->firstOrFail();

        $investments = \App\Models\Investment::where('user_id', $investor->id)
            ->whereIn('status', ['ACTIVE', 'PENDING', 'REJECTED', 'CLOSED'])
            ->latest()
            ->paginate(15);

        return view('user.network.investor_investments', compact('investor', 'investments'));
    }
}
