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
    public function active()
    {
        $investments = Investment::where('user_id', Auth::id())
            ->whereIn('status', [Investment::STATUS_PENDING, Investment::STATUS_ACTIVE])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.investments.active', compact('investments'));
    }

    public function closed()
    {
        $investments = Investment::where('user_id', Auth::id())
            ->whereIn('status', [Investment::STATUS_COMPLETED, Investment::STATUS_CLOSED, Investment::STATUS_REJECTED])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.investments.closed', compact('investments'));
    }

    public function closeRequest($id)
    {
        $investment = Investment::where('user_id', Auth::id())->findOrFail($id);

        if ($investment->status !== Investment::STATUS_ACTIVE) {
            return back()->with('error', 'Only active investments can be closed.');
        }

        $investment->status = Investment::STATUS_CLOSE_REQUEST;
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
