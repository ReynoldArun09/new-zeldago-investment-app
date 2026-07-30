<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Kyc;
use App\Models\Nominee;
use App\Models\BankDetail;
use App\Models\Admin;
use App\Notifications\GenericNotification;

class VerificationController extends Controller
{
    // KYC Methods
    public function kyc()
    {
        $kyc = Kyc::where('user_id', Auth::id())->latest()->first();
        return view('user.verification.kyc', compact('kyc'));
    }

    // Nominee Methods
    public function nominee()
    {
        $nominee = Nominee::where('user_id', Auth::id())->latest()->first();
        return view('user.verification.nominee', compact('nominee'));
    }

    // Bank Details Methods
    public function bank()
    {
        $bankDetail = BankDetail::where('user_id', Auth::id())->first();
        return view('user.verification.bank', compact('bankDetail'));
    }

    public function confirmBank()
    {
        $bankDetail = BankDetail::where('user_id', Auth::id())->first();
        
        if ($bankDetail && $bankDetail->status === 'PENDING') {
            $bankDetail->status = 'APPROVED';
            $bankDetail->save();
            return back()->with('success', 'Bank details confirmed successfully.');
        }
        
        return back()->withErrors(['Error confirming bank details.']);
    }
}
