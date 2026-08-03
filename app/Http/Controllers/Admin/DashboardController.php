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
        $totalInvestors   = User::where('account_type', 'Normal User')->count();
        $totalAgents      = User::where('account_type', 'Agent')->count();
        $activeMembers    = User::where('is_active', true)->count();
        $newRegistrations = User::where('created_at', '>=', now()->subDays(7))->count();

        // Business
        $totalBusiness          = Investment::whereNotIn('status', [Investment::STATUS_PENDING, Investment::STATUS_REJECTED])->sum('amount') ?? 0;
        $totalPendingBusiness   = Investment::where('status', Investment::STATUS_PENDING)->sum('amount') ?? 0;
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

        // Payouts
        $totalRoiPaid = \App\Models\Transaction::where('type', 'ROI')->sum('amount') ?? 0;
        $totalCommissionPaid = \App\Models\Transaction::whereIn('type', ['COMMISSION', 'commission', 'transfer_in', 'transfer_out'])->sum('amount') ?? 0;

        $stats = [
            'totalInvestors'      => $totalInvestors,
            'totalAgents'         => $totalAgents,
            'activeMembers'       => $activeMembers,
            'newRegistrations'    => $newRegistrations,
            'totalBusiness'       => $totalBusiness,
            'totalPendingBusiness'=> $totalPendingBusiness,
            'pendingInvestments'  => $pendingInvestments,
            'totalRoiPaid'        => $totalRoiPaid,
            'totalCommissionPaid' => $totalCommissionPaid,
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
