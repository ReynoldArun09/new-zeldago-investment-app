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

        // Network Total Investments
        $network_investments = \App\Models\Investment::whereHas('user', function($q) use ($user) {
            $q->where('sponsor_id', $user->id);
        })->sum('amount');

        // Total ROI Received
        $total_roi = \App\Models\RoiLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->sum('amount');
            
        // Calculate total commissions (Level Bonuses)
        $total_commissions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['commission', 'transfer_in'])
            ->sum('amount');
        
        // Recent commissions and transfers
        $recent_commissions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['commission', 'transfer_in', 'transfer_out'])
            ->latest()
            ->take(5)
            ->get();
            
        // Recent ROI returns
        $recent_rois = \App\Models\RoiLog::with('investment')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
            
        // Recent Direct ROI returns
        $recent_direct_rois = \App\Models\RoiLog::with('investment')
            ->where('user_id', $user->id)
            ->where('direct_roi_amount', '>', 0)
            ->latest()
            ->take(5)
            ->get();

        // Normal Investments for user
        $user_investments = \App\Models\Investment::with('roiLogs')->where('user_id', $user->id)
            ->where('is_old', false)
            ->latest()
            ->get();
            


        // Investment Counts
        $active_investments_count = \App\Models\Investment::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->count();

        $closed_investments_count = \App\Models\Investment::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->count();
            
        $old_investments_amount = \App\Models\Investment::where('user_id', $user->id)
            ->where('is_old', true)
            ->sum('amount');
            
        $total_direct_roi = \App\Models\RoiLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->sum('direct_roi_amount');
            
        // Recent Withdrawals
        $recent_withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $agent_investors = collect();
        $agent_investor_investments = collect();
        if ($user->account_type === 'Agent') {
            $agent_investors = \App\Models\User::where('sponsor_id', $user->id)->get();
            $agent_investor_investments = \App\Models\Investment::with('user')
                ->whereIn('user_id', $agent_investors->pluck('id'))
                ->latest()
                ->take(10)
                ->get();
        }

        return view('user.dashboard', compact(
            'wallet_balance', 
            'active_referrals', 
            'total_commissions',
            'total_investment',
            'total_roi',
            'network_investments',
            'recent_commissions',
            'recent_rois',
            'recent_direct_rois',
            'user_investments',

            'active_investments_count',
            'closed_investments_count',
            'old_investments_amount',
            'total_direct_roi',
            'recent_withdrawals',
            'agent_investors',
            'agent_investor_investments'
        ));
    }

    public function downloadStatements()
    {
        $user = Auth::user();
        $fileName = 'statements_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [];
        $data = [];

        if ($user->account_type === 'Agent') {
            $columns = ['Date', 'Transaction ID', 'Amount', 'Type', 'Description'];
            $transactions = Transaction::where('user_id', $user->id)
                ->latest()
                ->get();
                
            foreach ($transactions as $txn) {
                $data[] = [
                    $txn->created_at->format('Y-m-d H:i:s'),
                    $txn->trx_id,
                    $txn->amount,
                    $txn->type,
                    $txn->description
                ];
            }
        } else {
            $columns = ['Date', 'Transaction ID', 'Plan', 'Amount', 'Status'];
            $investments = \App\Models\Investment::where('user_id', $user->id)
                ->latest()
                ->get();

            foreach ($investments as $inv) {
                $data[] = [
                    $inv->created_at->format('Y-m-d H:i:s'),
                    $inv->trx_id,
                    'Investment',
                    $inv->amount,
                    $inv->status
                ];
            }
        }

        $callback = function() use($columns, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
