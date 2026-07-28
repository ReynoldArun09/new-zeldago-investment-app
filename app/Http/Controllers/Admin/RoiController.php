<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoiLog;
use App\Models\Setting;

class RoiController extends Controller
{
    /**
     * Show all ROI logs (ROI Payment Log)
     */
    public function index(Request $request)
    {
        $query = RoiLog::with(['user', 'investment'])->orderByDesc('created_at');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20);

        // Stats
        $totalRoiPaid = RoiLog::where('status', 'credited')->sum('amount');
        $totalRecords = RoiLog::count();
        $creditedCount = RoiLog::where('status', 'credited')->count();
        $pendingCount = RoiLog::where('status', 'pending')->count();

        return view('admin.roi.index', compact('logs', 'totalRoiPaid', 'totalRecords', 'creditedCount', 'pendingCount'));
    }

    /**
     * Show pending ROI requests
     */
    public function pending(Request $request)
    {
        $query = RoiLog::with(['user', 'investment'])->where('status', 'pending')->orderByDesc('created_at');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20);

        $roiSettings = Setting::where('key', 'roi_settings')->value('value') ?? [];

        return view('admin.roi.pending', compact('logs', 'roiSettings'));
    }

    /**
     * Approve a pending ROI request
     */
    public function approve(Request $request, $id)
    {
        $roiLog = RoiLog::with(['user', 'investment'])->findOrFail($id);

        if ($roiLog->status !== 'pending') {
            return redirect()->back()->with('error', 'ROI is not in pending status.');
        }

        $roiAmount = 0;
        
        if ($request->has('amount') && $request->filled('amount')) {
            $roiAmount = (float) $request->amount;
            $roiLog->rate = 0;
        } elseif ($request->has('rate') && $request->filled('rate')) {
            $roiLog->rate = (float) $request->rate;
            $roiAmount = ($roiLog->investment->amount * $roiLog->rate) / 100;
        }

        if ($roiAmount <= 0) {
            return redirect()->back()->with('error', 'Invalid ROI amount or rate provided.');
        }

        $roiLog->amount = $roiAmount;
        $roiLog->status = 'credited';
        $roiLog->save();

        $user = $roiLog->user;
        $user->wallet_balance = ($user->wallet_balance ?? 0) + $roiAmount;
        $user->save();
        
        // Log transaction for ROI earning
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $roiAmount,
            'type' => 'ROI',
            'description' => 'ROI Credited',
            'reference_id' => $roiLog->trx_id,
        ]);

        // Distribute MLM Commission based on ROI Amount
        $this->distributeRoiCommission($user, $roiAmount, $roiLog);

        return redirect()->back()->with('success', 'ROI approved and credited to user.');
    }

    private function distributeRoiCommission($user, $roiAmount, $roiLog)
    {
        $commissionSetting = \App\Models\CommissionSetting::first();
        if (!$commissionSetting) return;
        
        $levels = $commissionSetting->commissions ?? [];
        
        $currentSponsorId = $user->sponsor_id;
        $level = 1;
        
        while ($currentSponsorId && $level <= $commissionSetting->level_count) {
            $sponsor = \App\Models\User::find($currentSponsorId);
            if (!$sponsor) break;
            
            $percentage = $levels[$level] ?? 0;
            if ($percentage > 0) {
                $commissionAmount = ($roiAmount * $percentage) / 100;
                
                $sponsor->wallet_balance = ($sponsor->wallet_balance ?? 0) + $commissionAmount;
                $sponsor->save();
                
                \App\Models\Transaction::create([
                    'trx_id' => 'TRX-' . strtoupper(\Illuminate\Support\Str::random(10)),
                    'user_id' => $sponsor->id,
                    'amount' => $commissionAmount,
                    'type' => 'COMMISSION',
                    'description' => 'ROI Commission from Level ' . $level,
                    'reference_id' => $roiLog->trx_id,
                    'status' => 'COMPLETED',
                ]);
            }
            
            $currentSponsorId = $sponsor->sponsor_id;
            $level++;
        }
    }

    /**
     * Reject a pending ROI request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_note' => 'required|string|max:500',
        ]);

        $roiLog = RoiLog::with('user')->findOrFail($id);

        if ($roiLog->status !== 'pending') {
            return redirect()->back()->with('error', 'ROI is not in pending status.');
        }

        $roiLog->status = 'rejected';
        $roiLog->save();

        if ($roiLog->user) {
            $roiLog->user->notify(new \App\Notifications\GenericNotification(
                'ROI Request Rejected',
                'Your ROI request (' . $roiLog->trx_id . ') has been rejected. Reason: ' . $request->reject_note
            ));
        }

        return redirect()->back()->with('success', 'ROI request rejected and user notified.');
    }
}
