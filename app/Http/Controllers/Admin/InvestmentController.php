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
                'active' => 'ACTIVE',
                'completed' => 'COMPLETED',
                'closed' => 'CLOSED',
                'close-requests' => 'CLOSE_REQUEST',
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
        $investment->status = 'ACTIVE';
        
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
        }

        return redirect()->back()->with('success', 'Investment has been approved successfully.');
    }

    /**
     * Reject investment
     */
    public function reject($id)
    {
        $investment = Investment::findOrFail($id);
        $investment->status = 'REJECTED';
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
