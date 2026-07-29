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

    public function submitKyc(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|max:100',
            'document_number' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'document_front_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document_back_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $frontPath = $request->file('document_front_proof')->store('kyc_proofs', 'public');
        $backPath = $request->file('document_back_proof')->store('kyc_proofs', 'public');

        Kyc::create([
            'user_id' => Auth::id(),
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'country' => $request->country,
            'address' => $request->address,
            'document_front_proof' => $frontPath,
            'document_back_proof' => $backPath,
            'status' => 'pending'
        ]);

        $admins = Admin::all();
        if ($admins->count() > 0) {
            Notification::send($admins, new GenericNotification(
                'New KYC Submission',
                Auth::user()->username . ' submitted their KYC documents for review.',
                'ph-identification-card'
            ));
        }

        return back()->with('success', 'KYC document submitted successfully. Waiting for admin approval.');
    }

    // Nominee Methods
    public function nominee()
    {
        $nominee = Nominee::where('user_id', Auth::id())->latest()->first();
        return view('user.verification.nominee', compact('nominee'));
    }

    public function submitNominee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'identity_front_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identity_back_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $frontPath = $request->file('identity_front_proof')->store('nominee_proofs', 'public');
        $backPath = $request->file('identity_back_proof')->store('nominee_proofs', 'public');

        Nominee::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'relation' => $request->relation,
            'identity_front_proof' => $frontPath,
            'identity_back_proof' => $backPath,
            'status' => 'pending'
        ]);

        $admins = Admin::all();
        if ($admins->count() > 0) {
            Notification::send($admins, new GenericNotification(
                'New Nominee Submission',
                Auth::user()->username . ' submitted their nominee details for review.',
                'ph-users'
            ));
        }

        return back()->with('success', 'Nominee details submitted successfully. Waiting for admin approval.');
    }

    // Bank Details Methods
    public function bank()
    {
        $bankDetail = BankDetail::where('user_id', Auth::id())->first();
        return view('user.verification.bank', compact('bankDetail'));
    }

    public function bankStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'upi_id' => 'nullable|string|max:255',
            'upi_number' => 'nullable|string|max:255',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bankDetail = BankDetail::where('user_id', Auth::id())->first();
        
        $proofPath = $bankDetail ? $bankDetail->proof_image : null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('bank_proofs', 'public');
        }

        BankDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => $request->name,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
                'upi_number' => $request->upi_number,
                'proof_image' => $proofPath,
                'status' => 'PENDING',
                'rejection_reason' => null
            ]
        );

        $admins = Admin::all();
        if ($admins->count() > 0) {
            Notification::send($admins, new GenericNotification(
                'New Bank Details Submission',
                Auth::user()->username . ' submitted their bank details for review.',
                'ph-bank'
            ));
        }

        return back()->with('success', 'Bank details saved successfully. Waiting for admin verification.');
    }
}
