<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Investment;
use App\Models\Admin;
use App\Notifications\GenericNotification;
use Illuminate\Support\Str;

class InvestmentController extends Controller
{
    public function create()
    {
        return view('user.investments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'trx_id' => 'required|string|max:255|unique:investments,trx_id',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('proofs', 'public');

        $investment = Investment::create([
            'user_id' => Auth::id(),
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'type' => 'manual',
            'status' => 'pending',
            'payment_proof' => $path,
        ]);

        $admins = Admin::all();
        if ($admins->count() > 0) {
            Notification::send($admins, new GenericNotification(
                'New Investment',
                Auth::user()->username . ' submitted a new investment of ' . format_currency($request->amount) . ' for review.',
                'ph-currency-dollar'
            ));
        }

        return redirect()->route('user.investments.active')->with('success', 'Investment request submitted successfully! It is currently pending review.');
    }

    public function active()
    {
        $investments = Investment::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'active'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.investments.active', compact('investments'));
    }

    public function closed()
    {
        $investments = Investment::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'closed', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.investments.closed', compact('investments'));
    }

    public function closeRequest($id)
    {
        $investment = Investment::where('user_id', Auth::id())->findOrFail($id);

        if ($investment->status !== 'ACTIVE') {
            return back()->with('error', 'Only active investments can be closed.');
        }

        $investment->status = 'CLOSE_REQUEST';
        $investment->save();

        $admins = Admin::all();
        if ($admins->count() > 0) {
            Notification::send($admins, new GenericNotification(
                'Investment Close Request',
                Auth::user()->username . ' has requested to close their investment of ' . format_currency($investment->amount) . '.',
                'ph-hand-palm'
            ));
        }

        return back()->with('success', 'Close request submitted successfully. Waiting for admin approval.');
    }
}
