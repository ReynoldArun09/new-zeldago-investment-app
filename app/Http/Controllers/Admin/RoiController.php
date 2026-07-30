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
     * Show processing ROI requests
     */
    public function processing(Request $request)
    {
        $query = RoiLog::with(['user', 'investment'])->where('status', 'processing')->orderByDesc('created_at');

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

        return view('admin.roi.processing', compact('logs', 'roiSettings'));
    }

    /**
     * Move a pending ROI request to processing
     */
    public function process(Request $request, $id)
    {
        $roiLog = RoiLog::findOrFail($id);

        if ($roiLog->status !== 'pending') {
            return redirect()->back()->with('error', 'ROI is not in pending status.');
        }

        $roiLog->status = 'processing';
        $roiLog->save();

        return redirect()->back()->with('success', 'ROI request is now processing.');
    }

    /**
     * Approve a pending ROI request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_trx_id' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $roiLog = RoiLog::with(['user', 'investment'])->findOrFail($id);

        if ($roiLog->status !== 'processing') {
            return redirect()->back()->with('error', 'ROI is not in processing status.');
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

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('roi_proofs', 'public');
        }

        $roiLog->amount = $roiAmount;
        $roiLog->payment_method = $request->payment_method;
        $roiLog->payment_trx_id = $request->payment_trx_id;
        $roiLog->payment_proof = $proofPath;
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

        return redirect()->back()->with('success', 'ROI approved and credited to user.');
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

        if (!in_array($roiLog->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'ROI cannot be rejected from its current status.');
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
