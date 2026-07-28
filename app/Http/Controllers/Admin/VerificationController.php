<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kyc;
use App\Models\Nominee;
use App\Notifications\VerificationStatusNotification;

class VerificationController extends Controller
{
    // KYC Management
    public function kycReview($id)
    {
        $kyc = Kyc::with('user')->findOrFail($id);
        return view('admin.verification.kyc-review', compact('kyc'));
    }

    public function kycList(Request $request, $status = null)
    {
        $query = Kyc::with('user')->latest();
        
        if ($status) {
            $query->where('status', $status);
        }

        $kycs = $query->paginate(15);
        
        $title = $status ? ucfirst($status) . ' KYC' : 'All KYC';

        return view('admin.verification.kyc', compact('kycs', 'title'));
    }

    public function kycUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_message' => 'nullable|string'
        ]);

        $kyc = Kyc::with('user')->findOrFail($id);
        $kyc->status = $request->status;
        $kyc->admin_message = $request->admin_message;
        $kyc->save();

        // Notify user
        $title = 'KYC ' . ucfirst($kyc->status);
        $message = 'Your KYC document was ' . $kyc->status . '.';
        if ($kyc->admin_message) {
            $message .= ' Reason: ' . $kyc->admin_message;
        }

        // Notification data
        $kyc->user->notify(new \App\Notifications\GenericNotification($title, $message, $kyc->status === 'approved' ? 'ph-shield-check' : 'ph-warning-circle'));

        return back()->with('success', 'KYC status updated successfully.');
    }

    // Nominee Management
    public function nomineeReview($id)
    {
        $nominee = Nominee::with('user')->findOrFail($id);
        return view('admin.verification.nominee-review', compact('nominee'));
    }

    public function nomineeList(Request $request, $status = null)
    {
        $query = Nominee::with('user')->latest();
        
        if ($status) {
            $query->where('status', $status);
        }

        $nominees = $query->paginate(15);
        
        $title = $status ? ucfirst($status) . ' Nominees' : 'All Nominees';

        return view('admin.verification.nominee', compact('nominees', 'title'));
    }

    public function nomineeUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_message' => 'nullable|string'
        ]);

        $nominee = Nominee::with('user')->findOrFail($id);
        $nominee->status = $request->status;
        $nominee->admin_message = $request->admin_message;
        $nominee->save();

        // Notify user
        $title = 'Nominee ' . ucfirst($nominee->status);
        $message = 'Your nominee details were ' . $nominee->status . '.';
        if ($nominee->admin_message) {
            $message .= ' Reason: ' . $nominee->admin_message;
        }

        $nominee->user->notify(new \App\Notifications\GenericNotification($title, $message, $nominee->status === 'approved' ? 'ph-shield-check' : 'ph-warning-circle'));

        return back()->with('success', 'Nominee status updated successfully.');
    }
}
