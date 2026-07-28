<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Withdrawal;

class FinanceController extends Controller
{
    public function transactions()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('user.finance.transactions', compact('transactions'));
    }

    public function withdrawals()
    {
        $withdrawals = Withdrawal::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('user.finance.withdrawals', compact('withdrawals'));
    }

    public function submitWithdrawal(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'amount' => 'required|numeric|min:10|max:' . $user->wallet_balance,
            'payout_method' => 'required|string|max:50',
            'payout_details' => 'required|string',
        ]);

        // Deduct balance
        $user->wallet_balance -= $request->amount;
        $user->save();

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payout_method' => $request->payout_method,
            'payout_details' => $request->payout_details,
            'status' => 'pending',
        ]);

        // Log transaction
        Transaction::create([
            'user_id' => $user->id,
            'amount' => -$request->amount,
            'type' => 'withdrawal',
            'description' => 'Withdrawal request via ' . $request->payout_method,
            'reference_id' => $withdrawal->id,
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully. Waiting for admin approval.');
    }
}
