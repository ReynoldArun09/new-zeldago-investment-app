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

    public function storeKyc(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|max:100',
            'document_number' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'document_front_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document_back_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $kyc = Kyc::where('user_id', Auth::id())->first() ?? new Kyc(['user_id' => Auth::id()]);
        
        // Prevent editing if already pending or approved
        if (in_array($kyc->status, ['pending', 'approved'])) {
            return back()->withErrors(['Your KYC details are already ' . $kyc->status . ' and cannot be edited.']);
        }

        $kyc->document_type = $request->document_type;
        $kyc->document_number = $request->document_number;
        $kyc->country = $request->country;
        $kyc->address = $request->address;

        if ($request->hasFile('document_front_proof')) {
            $kyc->document_front_proof = $request->file('document_front_proof')->store('kyc_proofs', 'public');
        }
        if ($request->hasFile('document_back_proof')) {
            $kyc->document_back_proof = $request->file('document_back_proof')->store('kyc_proofs', 'public');
        }

        $kyc->status = 'pending';
        $kyc->save();

        return back()->with('success', 'KYC details submitted successfully. It is pending review.');
    }

    // Nominee Methods
    public function nominee()
    {
        $nominee = Nominee::where('user_id', Auth::id())->latest()->first();
        return view('user.verification.nominee', compact('nominee'));
    }

    public function storeNominee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'identity_front_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identity_back_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $nominee = Nominee::where('user_id', Auth::id())->first() ?? new Nominee(['user_id' => Auth::id()]);
        
        if (in_array($nominee->status, ['pending', 'approved'])) {
            return back()->withErrors(['Your Nominee details are already ' . $nominee->status . ' and cannot be edited.']);
        }

        $nominee->name = $request->name;
        $nominee->relation = $request->relation;

        if ($request->hasFile('identity_front_proof')) {
            $nominee->identity_front_proof = $request->file('identity_front_proof')->store('nominee_proofs', 'public');
        }
        if ($request->hasFile('identity_back_proof')) {
            $nominee->identity_back_proof = $request->file('identity_back_proof')->store('nominee_proofs', 'public');
        }

        $nominee->status = 'pending';
        $nominee->save();

        return back()->with('success', 'Nominee details submitted successfully. It is pending review.');
    }

    // Bank Details Methods
    public function bank()
    {
        $bankDetail = BankDetail::where('user_id', Auth::id())->first();
        return view('user.verification.bank', compact('bankDetail'));
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'upi_id' => 'nullable|string|max:255',
            'upi_number' => 'nullable|string|max:255',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bank = BankDetail::where('user_id', Auth::id())->first() ?? new BankDetail(['user_id' => Auth::id()]);
        
        if (in_array(strtoupper($bank->status), ['PENDING', 'APPROVED'])) {
            return back()->withErrors(['Your Bank details are already ' . $bank->status . ' and cannot be edited.']);
        }

        $bank->name = $request->name;
        $bank->bank_name = $request->bank_name;
        $bank->account_number = $request->account_number;
        $bank->ifsc_code = $request->ifsc_code;
        $bank->upi_id = $request->upi_id;
        $bank->upi_number = $request->upi_number;

        if ($request->hasFile('proof_image')) {
            $bank->proof_image = $request->file('proof_image')->store('bank_proofs', 'public');
        }

        $bank->status = 'PENDING';
        $bank->save();

        return back()->with('success', 'Bank details submitted successfully. It is pending review.');
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
