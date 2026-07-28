<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Transaction;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('user')->orderBy('created_at', 'desc');
        // stats
        $totalWithdrawals = Withdrawal::sum('amount');
        $approvedCount = Withdrawal::where('status', 'approved')->count();
        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $withdrawals = $query->paginate(20);
        
        return view('admin.withdrawals.index', compact('withdrawals', 'totalWithdrawals', 'approvedCount', 'pendingCount'));
    }

    public function pending(Request $request)
    {
        $query = Withdrawal::with('user')->where('status', 'pending')->orderBy('created_at', 'desc');
        $withdrawals = $query->paginate(20);
        return view('admin.withdrawals.pending', compact('withdrawals'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'trx_id' => 'required|string|max:255',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $imageName = time() . '_' . str_replace(' ', '_', $request->file('proof_image')->getClientOriginalName());
        $proofPath = $request->file('proof_image')->storeAs('proofs', $imageName, 'public');

        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id, $proofPath) {
            $withdrawal = Withdrawal::with('user')->lockForUpdate()->findOrFail($id);
            if ($withdrawal->status !== 'pending') {
                return false;
            }

            $withdrawal->status = 'approved';
            $withdrawal->trx_id = $request->trx_id;
            $withdrawal->proof_image = $proofPath;
            $withdrawal->save();
            
            return $withdrawal;
        });

        if (!$result) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($proofPath);
            return back()->with('error', 'Withdrawal is not pending.');
        }

        if ($result->user) {
            $currency = get_setting('currency_symbol', '$');
            $result->user->notify(new \App\Notifications\GenericNotification(
                'Withdrawal Approved',
                "Your withdrawal of " . $currency . number_format($result->amount, 2) . " has been approved. Transaction ID: " . $result->trx_id
            ));
        }

        return back()->with('success', 'Withdrawal approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_note' => 'required|string|max:500'
        ]);

        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id) {
            $withdrawal = Withdrawal::with('user')->lockForUpdate()->findOrFail($id);
            if ($withdrawal->status !== 'pending') {
                return false;
            }

            $withdrawal->status = 'rejected';
            $withdrawal->admin_message = $request->reject_note;
            $withdrawal->save();

            // Refund to wallet_balance
            $user = $withdrawal->user;
            if ($user) {
                $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
                $lockedUser->wallet_balance += $withdrawal->amount;
                $lockedUser->save();

                Transaction::create([
                    'user_id' => $lockedUser->id,
                    'amount' => $withdrawal->amount,
                    'type' => 'withdrawal_refund',
                    'description' => 'Refund for rejected withdrawal.',
                    'reference_id' => $withdrawal->id,
                ]);
            }
            
            return $withdrawal;
        });

        if (!$result) {
            return back()->with('error', 'Withdrawal is not pending.');
        }

        if ($result->user) {
            $currency = get_setting('currency_symbol', '$');
            $result->user->notify(new \App\Notifications\GenericNotification(
                'Withdrawal Rejected',
                "Your withdrawal of " . $currency . number_format($result->amount, 2) . " was rejected and refunded to your wallet. Reason: " . $request->reject_note
            ));
        }

        return back()->with('success', 'Withdrawal rejected and refunded.');
    }
}
