<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\RoiLog;
use App\Models\CommissionSetting;
use Carbon\Carbon;

class CommissionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user'])->whereIn('type', ['COMMISSION', 'commission', 'transfer_in'])->latest();

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $commissions = $query->paginate(20);
        
        $referenceIds = $commissions->pluck('reference_id')->filter()->toArray();
        $sourceInvestments = \App\Models\Investment::with(['user'])->whereIn('trx_id', $referenceIds)->get()->keyBy('trx_id');
        
        $roiLogs = \App\Models\RoiLog::with(['investment.user'])->whereIn('trx_id', $referenceIds)->get();
        foreach ($roiLogs as $roiLog) {
            if ($roiLog->investment) {
                $sourceInvestments[$roiLog->trx_id] = $roiLog->investment;
            }
        }
        
        $commissionSetting = CommissionSetting::first();
        $totalLevels = $commissionSetting ? $commissionSetting->level_count : 4;
        $levelTotals = [];
        
        for ($i = 1; $i <= $totalLevels; $i++) {
            $levelTotals[$i] = Transaction::whereIn('type', ['COMMISSION', 'commission', 'transfer_in'])
                ->where('description', 'like', "%Level {$i}%")
                ->sum('amount');
        }

        $totalPaid = Transaction::whereIn('type', ['COMMISSION', 'commission', 'transfer_in'])->sum('amount');

        return view('admin.commissions.log', compact('commissions', 'sourceInvestments', 'levelTotals', 'totalPaid'));
    }
}
