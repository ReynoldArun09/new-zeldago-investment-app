<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;

class DashboardController extends Controller
{
    public function index()
    {
        // Members
        $totalMembers     = User::where('role', 'INVESTOR')->count();
        $activeMembers    = User::where('role', 'INVESTOR')->where('is_active', true)->count();
        $newRegistrations = User::whereIn('role', ['INVESTOR', 'AGENT'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Business
        $totalBusiness          = Investment::sum('amount') ?? 0;
        $pendingInvestments     = Investment::where('status', 'PENDING')->count();
        $completedInvestments   = Investment::where('status', 'COMPLETED')->count();
        $closeRequests          = Investment::where('status', 'CLOSE_REQUEST')->count();

        // Pending ROI requests
        $pendingRois = \App\Models\RoiLog::with(['user', 'investment'])
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pending KYCs
        $pendingKycs = \App\Models\Kyc::with('user')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Open Tickets
        $openTickets = \App\Models\Ticket::with('user')
            ->where('status', 'OPEN')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Investments
        $recentInvestments = \App\Models\Investment::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pending Withdrawals
        $pendingWithdrawalsList = \App\Models\Withdrawal::with('user')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'totalMembers'        => $totalMembers,
            'activeMembers'       => $activeMembers,
            'newRegistrations'    => $newRegistrations,
            'totalBusiness'       => $totalBusiness,
            'totalIncomePaid'     => 0,
            'pendingWithdrawals'  => 0,
            'totalWithdrawn'      => 0,
            'rejectedWithdrawals' => 0,
            'withdrawalCharge'    => 0,
            'completedInvestments'=> $completedInvestments,
            'closeRequests'       => $closeRequests,
        ];

        $roiSettings = \App\Models\Setting::where('key', 'roi_settings')->value('value') ?? [];

        return view('admin.dashboard', compact('stats', 'pendingRois', 'pendingKycs', 'openTickets', 'recentInvestments', 'pendingWithdrawalsList', 'roiSettings'));
    }
}
