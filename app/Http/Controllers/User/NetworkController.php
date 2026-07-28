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
}
