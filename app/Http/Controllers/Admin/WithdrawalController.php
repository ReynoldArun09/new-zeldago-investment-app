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

        $withdrawal = Withdrawal::with('user')->findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Withdrawal is not pending.');
        }

        $imageName = time() . '_' . str_replace(' ', '_', $request->file('proof_image')->getClientOriginalName());
        $request->file('proof_image')->move(public_path('uploads/proofs'), $imageName);
        $proofPath = 'uploads/proofs/' . $imageName;

        $withdrawal->status = 'approved';
        $withdrawal->trx_id = $request->trx_id;
        $withdrawal->proof_image = $proofPath;
        $withdrawal->save();

        if ($withdrawal->user) {
            $withdrawal->user->notify(new \App\Notifications\GenericNotification(
                'Withdrawal Approved',
                "Your withdrawal of $" . number_format($withdrawal->amount, 2) . " has been approved. Transaction ID: " . $withdrawal->trx_id
            ));
        }

        return back()->with('success', 'Withdrawal approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_note' => 'required|string|max:500'
        ]);

        $withdrawal = Withdrawal::with('user')->findOrFail($id);
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Withdrawal is not pending.');
        }

        $withdrawal->status = 'rejected';
        $withdrawal->admin_message = $request->reject_note;
        $withdrawal->save();

        // Refund to wallet_balance
        $user = $withdrawal->user;
        if ($user) {
            $user->wallet_balance += $withdrawal->amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $withdrawal->amount,
                'type' => 'withdrawal_refund',
                'description' => 'Refund for rejected withdrawal.',
                'reference_id' => $withdrawal->id,
            ]);

            $user->notify(new \App\Notifications\GenericNotification(
                'Withdrawal Rejected',
                "Your withdrawal of $" . number_format($withdrawal->amount, 2) . " was rejected and refunded to your wallet. Reason: " . $request->reject_note
            ));
        }

        return back()->with('success', 'Withdrawal rejected and refunded.');
    }
}
