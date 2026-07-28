<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\RoiLog;
use App\Models\CommissionSetting;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function investmentReport()
    {
        $totalInvested = Investment::sum('amount');
        $projectedReturns = Investment::sum('total_return');
        $roiPaidOut = RoiLog::sum('amount');

        $activeCount = Investment::where('status', 'ACTIVE')->count();
        $completedCount = Investment::where('status', 'COMPLETED')->count();
        $closedCount = Investment::where('status', 'CLOSED')->count();
        $totalStatusCount = $activeCount + $completedCount + $closedCount;
        $totalStatusCount = $totalStatusCount > 0 ? $totalStatusCount : 1; // prevent div by zero

        $topInvestors = User::select('users.id', 'users.username')
            ->join('investments', 'users.id', '=', 'investments.user_id')
            ->selectRaw('users.username, SUM(investments.amount) as total_volume')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_volume')
            ->limit(5)
            ->get();

        $commissionSetting = CommissionSetting::first();
        $levels = $commissionSetting ? $commissionSetting->level_count : 4;
        
        $commissionLevels = [];
        for ($i = 1; $i <= $levels; $i++) {
            // Assume we can sum transactions by description or type. We don't have level data tracked explicitly
            // so we will just show 0 for now unless we do.
            $commissionLevels[] = [
                'level' => $i,
                'amount' => 0,
                'transactions' => 0
            ];
        }

        return view('admin.reports.investment', compact(
            'totalInvested', 'projectedReturns', 'roiPaidOut',
            'activeCount', 'completedCount', 'closedCount', 'totalStatusCount',
            'topInvestors', 'commissionLevels'
        ));
    }

    public function roiReport()
    {
        $totalRoiPaid = RoiLog::sum('amount');
        $totalCredits = RoiLog::count();
        // Assuming avg ROI rate isn't directly in RoiLog if it just has amount, we calculate amount / investment amount
        // If there's a rate column, we'll just show 0 if not
        $avgRoiRate = 0; 
        $uniqueInvestors = RoiLog::distinct('user_id')->count('user_id');

        $monthlyPayouts = RoiLog::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(id) as credits, SUM(amount) as total_paid')
            ->groupBy('month')
            ->orderByDesc('month')
            ->get();

        return view('admin.reports.roi', compact(
            'totalRoiPaid', 'totalCredits', 'avgRoiRate', 'uniqueInvestors', 'monthlyPayouts'
        ));
    }

    public function commissionsReport()
    {
        $commissionSetting = CommissionSetting::first();
        $levels = $commissionSetting ? $commissionSetting->level_count : 4;
        
        $commissionLevels = [];
        $totalCommission = 0;
        for ($i = 1; $i <= $levels; $i++) {
            $commissionLevels[] = [
                'level' => $i,
                'amount' => 0,
                'transactions' => 0
            ];
        }

        $topEarners = User::select('users.id', 'users.username')
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.type', 'COMMISSION')
            ->selectRaw('SUM(transactions.amount) as total_earned')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_earned')
            ->limit(5)
            ->get();

        return view('admin.reports.commissions', compact('commissionLevels', 'totalCommission', 'topEarners'));
    }

    public function withdrawalsReport()
    {
        // Users Withdrawals only
        $approvedAmount = Withdrawal::where('status', 'APPROVED')->sum('amount');
        $pendingAmount = Withdrawal::where('status', 'PENDING')->sum('amount');

        $approvedCount = Withdrawal::where('status', 'APPROVED')->count();
        $pendingCount = Withdrawal::where('status', 'PENDING')->count();
        $rejectedCount = Withdrawal::where('status', 'REJECTED')->count();
        
        $rejectedAmount = Withdrawal::where('status', 'REJECTED')->sum('amount');
        
        $totalAmount = $approvedAmount + $pendingAmount + $rejectedAmount;
        $totalAmount = $totalAmount > 0 ? $totalAmount : 1;

        return view('admin.reports.withdrawals', compact(
            'approvedAmount', 'pendingAmount', 
            'approvedCount', 'pendingCount', 'rejectedCount',
            'rejectedAmount', 'totalAmount'
        ));
    }
}
