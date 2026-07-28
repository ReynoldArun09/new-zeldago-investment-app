<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch wallet balance
        $wallet_balance = $user->wallet_balance ?? 0;
        
        // Count active referrals (all direct referrals for now)
        $active_referrals = $user->directReferrals()->count();
        
        // Total Investment
        $total_investment = \App\Models\Investment::where('user_id', $user->id)
            ->whereIn('status', ['ACTIVE', 'COMPLETED'])
            ->sum('amount');

        // Total ROI Received
        $total_roi = \App\Models\RoiLog::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
            
        // Calculate total commissions (Level Bonuses)
        $total_commissions = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->sum('amount');
        
        // Recent commissions
        $recent_commissions = Transaction::where('user_id', $user->id)
            ->where('type', 'commission')
            ->latest()
            ->take(5)
            ->get();
            
        // Recent ROI returns
        $recent_rois = \App\Models\RoiLog::with('investment')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'wallet_balance', 
            'active_referrals', 
            'total_commissions',
            'total_investment',
            'total_roi',
            'recent_commissions',
            'recent_rois'
        ));
    }
}
