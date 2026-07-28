<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\RoiLog;
use App\Models\CommissionSetting;

class CommissionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user'])->where('type', 'COMMISSION')->latest();

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        $commissions = $query->paginate(20);
        
        $referenceIds = $commissions->pluck('reference_id')->filter()->toArray();
        $roiLogs = RoiLog::with(['user', 'investment'])->whereIn('trx_id', $referenceIds)->get()->keyBy('trx_id');
        
        $commissionSetting = CommissionSetting::first();
        $totalLevels = $commissionSetting ? $commissionSetting->level_count : 4;
        $levelTotals = [];
        
        for ($i = 1; $i <= $totalLevels; $i++) {
            $levelTotals[$i] = Transaction::where('type', 'COMMISSION')
                ->where('description', 'like', "%Level {$i}%")
                ->sum('amount');
        }

        $totalPaid = Transaction::where('type', 'COMMISSION')->sum('amount');

        return view('admin.commissions.log', compact('commissions', 'roiLogs', 'levelTotals', 'totalPaid'));
    }
}
