<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Notifications\GenericNotification;

class InvestmentController extends Controller
{
    /**
     * Show all investments, or filter by status
     */
    public function index(Request $request, $status = null)
    {
        $query = Investment::with('user')->latest();

        if ($status) {
            // Map the route parameter to the DB status
            // DB statuses: PENDING, ACTIVE, COMPLETED, CLOSED, CLOSE_REQUEST
            $dbStatus = match ($status) {
                'active' => Investment::STATUS_ACTIVE,
                'completed' => Investment::STATUS_COMPLETED,
                'closed' => Investment::STATUS_CLOSED,
                'close-requests' => Investment::STATUS_CLOSE_REQUEST,
                default => strtoupper($status),
            };
            
            $query->where('status', $dbStatus);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $investments = $query->paginate(20);
        
        $title = match ($status) {
            'active' => 'Active Investments',
            'completed' => 'Completed Investments',
            'closed' => 'Closed Investments',
            'close-requests' => 'Close Requests',
            default => 'All Investments',
        };

        return view('admin.investments.index', compact('investments', 'title', 'status'));
    }

    /**
     * Show investment details
     */
    public function show($id)
    {
        $investment = Investment::with('user')->findOrFail($id);
        return view('admin.investments.show', compact('investment'));
    }

    /**
     * Approve investment
     */
    public function approve($id)
    {
        $investment = Investment::findOrFail($id);
        $investment->status = Investment::STATUS_ACTIVE;
        
        $roiSetting = \App\Models\Setting::where('key', 'roi_settings')->first();
        $settings = $roiSetting ? $roiSetting->value : [];
        $cycleDays = (int) ($settings['cycle_days'] ?? 1);
        // Set the next ROI date based on settings
        $investment->next_roi_date = now()->addDays($cycleDays);
        
        $investment->save();

        if ($investment->user) {
            $investment->user->notify(new GenericNotification(
                'Investment Approved',
                'Your investment of ' . format_currency($investment->amount) . ' has been approved and is now active.',
                'ph-check-circle'
            ));
            
            // Distribute MLM Commission based on Investment Amount
            $this->distributeInvestmentCommission($investment->user, $investment->amount, $investment);
        }

        return redirect()->back()->with('success', 'Investment has been approved successfully.');
    }

    private function distributeInvestmentCommission($user, $investmentAmount, $investment)
    {
        $commissionSetting = \App\Models\CommissionSetting::first();
        if (!$commissionSetting) return;
        
        $levels = $commissionSetting->commissions ?? [];
        
        $currentSponsorId = $user->sponsor_id;
        $level = 1;
        
        while ($currentSponsorId && $level <= $commissionSetting->level_count) {
            $sponsor = \App\Models\User::find($currentSponsorId);
            if (!$sponsor) break;
            
            // Dynamic Compression: Skip Normal Users
            if (!in_array($sponsor->account_type, ['Agent', 'Root Distributor'])) {
                $currentSponsorId = $sponsor->sponsor_id;
                continue;
            }
            
            $percentage = $levels[$level] ?? 0;
            if ($percentage > 0) {
                $commissionAmount = ($investmentAmount * $percentage) / 100;
                
                $sponsor->wallet_balance = ($sponsor->wallet_balance ?? 0) + $commissionAmount;
                $sponsor->save();
                
                \App\Models\Transaction::create([
                    'trx_id' => 'TRX-' . strtoupper(\Illuminate\Support\Str::random(10)),
                    'user_id' => $sponsor->id,
                    'amount' => $commissionAmount,
                    'type' => 'COMMISSION',
                    'description' => 'Investment Commission from Level ' . $level,
                    'reference_id' => $investment->trx_id,
                    'status' => 'COMPLETED',
                ]);
            }
            
            $currentSponsorId = $sponsor->sponsor_id;
            $level++;
        }
    }

    /**
     * Reject investment
     */
    public function reject($id)
    {
        $investment = Investment::findOrFail($id);
        $investment->status = Investment::STATUS_REJECTED;
        $investment->save();

        if ($investment->user) {
            $investment->user->notify(new GenericNotification(
                'Investment Rejected',
                'Your investment of ' . format_currency($investment->amount) . ' has been rejected.',
                'ph-x-circle'
            ));
        }

        return redirect()->back()->with('success', 'Investment has been rejected.');
    }
}
