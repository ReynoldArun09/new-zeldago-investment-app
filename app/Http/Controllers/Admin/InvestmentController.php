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
        
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
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
     * Export investments to CSV
     */
    public function export(Request $request, $status = null)
    {
        $query = Investment::with('user')->latest();

        if ($status && $status !== 'all') {
            $dbStatus = match ($status) {
                'active' => Investment::STATUS_ACTIVE,
                'completed' => Investment::STATUS_COMPLETED,
                'closed' => Investment::STATUS_CLOSED,
                'close-requests' => Investment::STATUS_CLOSE_REQUEST,
                default => strtoupper($status),
            };
            $query->where('status', $dbStatus);
        } else if ($request->filled('status') && $request->status !== 'all') {
             $dbStatus = match ($request->status) {
                'active' => Investment::STATUS_ACTIVE,
                'completed' => Investment::STATUS_COMPLETED,
                'closed' => Investment::STATUS_CLOSED,
                'close-requests' => Investment::STATUS_CLOSE_REQUEST,
                default => strtoupper($request->status),
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

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $investments = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=investments_" . date('Y-m-d_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Trx ID', 'Date Started', 'User Name', 'Username', 'Initial Deposit', 'Next ROI Date', 'Status'];

        $callback = function() use($investments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($investments as $inv) {
                $row = [
                    $inv->trx_id,
                    $inv->created_at->format('Y-m-d H:i:s'),
                    $inv->user->name ?? 'N/A',
                    $inv->user->username ?? 'N/A',
                    $inv->amount,
                    $inv->next_roi_date ? \Carbon\Carbon::parse($inv->next_roi_date)->format('Y-m-d H:i:s') : 'N/A',
                    $inv->status
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
    }

    /**
     * Approve investment
     */
    public function approve($id)
    {
        $investment = Investment::findOrFail($id);
        $investment->status = Investment::STATUS_ACTIVE;
        
        if (!$investment->is_old) {
            $roiSetting = \App\Models\Setting::where('key', 'roi_settings')->first();
            $settings = $roiSetting ? $roiSetting->value : [];
            $cycleDays = (int) ($settings['cycle_days'] ?? 1);
            // Set the next ROI date based on settings
            $investment->next_roi_date = now()->addDays($cycleDays);
        }
        
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
