<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NetworkController extends Controller
{
    public function referrals()
    {
        $user = Auth::user();
        
        // Load the sponsor and direct referrals
        $user->load(['sponsor', 'directReferrals']);

        return view('user.network.referrals', compact('user'));
    }

    public function genealogy()
    {
        $user = Auth::user();
        
        // Eager load downline for the tree
        $user->load('downline');

        return view('user.network.genealogy', compact('user'));
    }

    public function addInvestor(Request $request)
    {
        if (Auth::user()->account_type !== 'Agent') {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $sponsor = Auth::user();

        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->phone = $request->phone;
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->account_type = 'Normal User';
        $user->sponsor_id = $sponsor->id;
        $user->save();

        return back()->with('success', 'Investor added successfully.');
    }

    public function investorInvestments($id)
    {
        $sponsor = Auth::user();
        $investor = \App\Models\User::where('id', $id)->where('sponsor_id', $sponsor->id)->firstOrFail();

        $investments = \App\Models\Investment::where('user_id', $investor->id)
            ->whereIn('status', ['ACTIVE', 'PENDING', 'REJECTED', 'CLOSED'])
            ->latest()
            ->paginate(15);

        return view('user.network.investor_investments', compact('investor', 'investments'));
    }

    public function addInvestment(Request $request)
    {
        if (Auth::user()->account_type !== 'Agent') {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'investor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'investment_date' => 'required|date',
            'roi_dates' => 'nullable|array',
            'roi_dates.*' => 'required|date',
            'roi_amounts' => 'nullable|array',
            'roi_amounts.*' => 'required|numeric|min:0',
        ]);

        $sponsor = Auth::user();
        $investor = \App\Models\User::where('id', $request->investor_id)->where('sponsor_id', $sponsor->id)->first();

        if (!$investor) {
            return back()->with('error', 'Invalid investor selected.');
        }

        // Generate unique TRX ID
        $trx_id = 'INV-' . strtoupper(uniqid());

        // Create the active investment with past dates
        $investment = new \App\Models\Investment();
        $investment->user_id = $investor->id;
        $investment->trx_id = $trx_id;
        $investment->amount = $request->amount;
        // Seeded properties
        $investment->status = \App\Models\Investment::STATUS_ACTIVE;
        $investment->roi_cycle_start_date = $request->investment_date;
        $investment->created_at = $request->investment_date;
        $investment->updated_at = $request->investment_date;
        $investment->save();

        // Create historical ROI logs
        $last_date = \Carbon\Carbon::parse($request->investment_date);

        if (!empty($request->roi_dates) && !empty($request->roi_amounts)) {
            foreach ($request->roi_dates as $index => $roi_date) {
                if (isset($request->roi_amounts[$index])) {
                    $roi_amount = $request->roi_amounts[$index];
                    
                    // Calculate roughly what rate this was based on the amount
                    $rate = 0;
                    if ($investment->amount > 0) {
                        $rate = round(($roi_amount / $investment->amount) * 100, 2);
                    }

                    $roi = new \App\Models\RoiLog();
                    $roi->trx_id = 'ROI-' . strtoupper(uniqid());
                    $roi->investment_id = $investment->id;
                    $roi->user_id = $investor->id;
                    $roi->amount = $roi_amount;
                    $roi->rate = $rate;
                    $roi->status = 'credited';
                    $roi->created_at = $roi_date;
                    $roi->updated_at = $roi_date;
                    $roi->save();

                    $last_date = \Carbon\Carbon::parse($roi_date);
                }
            }
        }

        $roiSetting = \App\Models\Setting::where('key', 'roi_settings')->first();
        $settings = $roiSetting ? $roiSetting->value : [];
        $cycleDays = (int) ($settings['cycle_days'] ?? 30);
        
        // Ensure cycleDays is greater than 0
        $cycleDays = $cycleDays > 0 ? $cycleDays : 30;

        $investment->next_roi_date = $last_date->copy()->addDays($cycleDays);
        $investment->save();

        return back()->with('success', 'Past investment and ROI records added successfully.');
    }
}
