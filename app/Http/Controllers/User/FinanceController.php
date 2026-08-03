<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Withdrawal;

class FinanceController extends Controller
{
    public function commissionTransactions()
    {
        if (Auth::user()->account_type !== 'Agent') {
            abort(403, 'Unauthorized action.');
        }

        $transactions = Transaction::where('user_id', Auth::id())
            ->where('type', 'commission')
            ->latest()
            ->paginate(15);
            
        $pageTitle = 'Commission Transactions';
        $pageSubtitle = 'A ledger of your earned affiliate commissions.';
            
        return view('user.finance.transactions', compact('transactions', 'pageTitle', 'pageSubtitle'));
    }

    public function roiTransactions()
    {
        if (Auth::user()->account_type !== 'Normal User') {
            abort(403, 'Unauthorized action.');
        }

        $roiLogs = \App\Models\RoiLog::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        $pageTitle = 'ROI Returns';
        $pageSubtitle = 'A ledger of your daily return on investments.';
            
        return view('user.finance.roi', compact('roiLogs', 'pageTitle', 'pageSubtitle'));
    }

    public function withdrawals()
    {
        if (Auth::user()->account_type !== 'Agent') {
            abort(403, 'Unauthorized action.');
        }

        $withdrawals = Withdrawal::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        $total_commissions = Transaction::where('user_id', Auth::id())
            ->where('type', 'commission')
            ->sum('amount');
            
        $transfers_in = Transaction::where('user_id', Auth::id())
            ->where('type', 'transfer_in')
            ->sum('amount');
            
        $total_commissions += $transfers_in;
            
        $withdrawn = Withdrawal::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
            
        $transfers_out = Transaction::where('user_id', Auth::id())
            ->where('type', 'transfer_out')
            ->sum('amount');
            
        $withdrawn += $transfers_out;
            
        $available_balance = min(Auth::user()->wallet_balance, max(0, $total_commissions - $withdrawn));
            
        return view('user.finance.withdrawals', compact('withdrawals', 'available_balance'));
    }

    public function transfer()
    {
        if (Auth::user()->account_type !== 'Agent') {
            abort(403, 'Unauthorized action.');
        }

        $total_commissions = Transaction::where('user_id', Auth::id())
            ->where('type', 'commission')
            ->sum('amount');
            
        $transfers_in = Transaction::where('user_id', Auth::id())
            ->where('type', 'transfer_in')
            ->sum('amount');
            
        $total_commissions += $transfers_in;
            
        $withdrawn = Withdrawal::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
            
        $transfers_out = Transaction::where('user_id', Auth::id())
            ->where('type', 'transfer_out')
            ->sum('amount');
            
        $withdrawn += $transfers_out;
            
        $available_balance = min(Auth::user()->wallet_balance, max(0, $total_commissions - $withdrawn));

        return view('user.finance.transfer', compact('available_balance'));
    }

    public function submitWithdrawal(Request $request)
    {
        if (Auth::user()->account_type !== 'Agent') {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();
        
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payout_method' => 'required|string|in:Cash,UPI,Bank Transfer',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $request) {
            $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
            
            $total_commissions = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'commission')
                ->sum('amount');
                
            $transfers_in = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'transfer_in')
                ->sum('amount');
                
            $total_commissions += $transfers_in;
                
            $withdrawn = Withdrawal::where('user_id', $lockedUser->id)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');
                
            $transfers_out = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'transfer_out')
                ->sum('amount');
                
            $withdrawn += $transfers_out;
                
            $available_balance = min($lockedUser->wallet_balance, max(0, $total_commissions - $withdrawn));
            
            if ($request->amount > $available_balance) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['The amount may not be greater than your available balance.'],
                ]);
            }

            // Deduct balance
            $lockedUser->wallet_balance -= $request->amount;
            $lockedUser->save();

            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $lockedUser->id,
                'amount' => $request->amount,
                'payout_method' => $request->payout_method,
                'payout_details' => '',
                'status' => 'pending',
            ]);

            // Log transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'amount' => -$request->amount,
                'type' => 'withdrawal',
                'description' => 'Withdrawal request via ' . $request->payout_method,
                'reference_id' => $withdrawal->id,
            ]);
        });

        // Notify Admins
        $admins = \App\Models\Admin::all();
        $currency = get_setting('currency_symbol', '$');
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\GenericNotification(
                'New Withdrawal Request',
                $user->name . ' has requested a withdrawal of ' . $currency . number_format($request->amount, 2),
                'ph-money'
            ));
        }

        return back()->with('success', 'Withdrawal request submitted successfully. Waiting for admin approval.');
    }

    public function searchAgent(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'mobile' => 'required|string'
        ]);

        $agent = \App\Models\User::where('username', $request->username)
            ->where('phone', $request->mobile)
            ->where('account_type', 'Agent')
            ->first();

        if (!$agent) {
            return response()->json(['success' => false, 'message' => 'Agent not found with the provided details.']);
        }

        if ($agent->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot transfer to yourself.']);
        }

        return response()->json([
            'success' => true, 
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'username' => $agent->username
            ]
        ]);
    }

    public function submitTransfer(Request $request)
    {
        if (Auth::user()->account_type !== 'Agent') {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();
        
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $recipient = \App\Models\User::where('id', $request->recipient_id)->where('account_type', 'Agent')->first();

        if (!$recipient || $recipient->id === $user->id) {
            return back()->with('error', 'Invalid recipient.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $recipient, $request) {
            $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
            $lockedRecipient = \App\Models\User::where('id', $recipient->id)->lockForUpdate()->first();
            
            $total_commissions = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'commission')
                ->sum('amount');
            
            $transfers_in = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'transfer_in')
                ->sum('amount');
                
            $total_commissions += $transfers_in;
                
            $withdrawn = Withdrawal::where('user_id', $lockedUser->id)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');

            $transfers_out = Transaction::where('user_id', $lockedUser->id)
                ->where('type', 'transfer_out')
                ->sum('amount');
                
            $withdrawn += $transfers_out;
                
            $available_balance = min($lockedUser->wallet_balance, max(0, $total_commissions - $withdrawn));
            
            if ($request->amount > $available_balance) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ['The amount may not be greater than your available balance.'],
                ]);
            }

            // Deduct balance from sender
            $lockedUser->wallet_balance -= $request->amount;
            $lockedUser->save();

            // Add balance to recipient
            $lockedRecipient->wallet_balance += $request->amount;
            $lockedRecipient->save();

            Transaction::create([
                'user_id' => $lockedUser->id,
                'amount' => -$request->amount,
                'type' => 'transfer_out',
                'description' => 'Transfer out to agent: ' . $lockedRecipient->username,
                'reference_id' => $lockedRecipient->id,
            ]);

            Transaction::create([
                'user_id' => $lockedRecipient->id,
                'amount' => $request->amount,
                'type' => 'transfer_in',
                'description' => 'Transfer received from agent: ' . $lockedUser->username,
                'reference_id' => $lockedUser->id,
            ]);
        });

        return back()->with('success', 'Transfer completed successfully.');
    }
}
