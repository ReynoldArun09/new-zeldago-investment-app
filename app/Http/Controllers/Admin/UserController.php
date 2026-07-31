<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\Kyc;
use App\Models\Nominee;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // ==========================================
    // USER MANAGEMENT LOGIC
    // ==========================================

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = 20;

        $query = User::with('sponsor')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function investors(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = 20;

        $query = User::with('sponsor')->where('account_type', 'Normal User')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.investors', compact('users', 'search'));
    }

    public function agents(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = 20;

        $query = User::with('sponsor')->where('account_type', 'Agent')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.agents', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'account_type' => 'required|in:Root Distributor,Normal User',
        ]);

        $sponsor_id = null;
        if ($request->account_type === 'Normal User') {
            $request->validate([
                'sponsor' => 'required|string'
            ]);
            
            $sponsor = User::where('username', $request->sponsor)
                            ->orWhere('referral_code', $request->sponsor)
                            ->first();

            if (!$sponsor) {
                return back()->withErrors(['sponsor' => 'Sponsor not found or inactive.'])->withInput();
            }
            if (!$sponsor->is_active) {
                return back()->withErrors(['sponsor' => 'Sponsor is inactive.'])->withInput();
            }
            
            $sponsor_id = $sponsor->id;
        }

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->account_type = $request->account_type;
        $user->sponsor_id = $sponsor_id;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show($identifier)
    {
        $user = User::with(['sponsor', 'directReferrals'])->where('username', $identifier)->orWhere('id', $identifier)->firstOrFail();

        $stats = [
            'roiReturns'           => \App\Models\Transaction::where('user_id', $user->id)->where('type', 'ROI')->sum('amount'),
            'myCommissions'        => \App\Models\Transaction::where('user_id', $user->id)->where('type', 'COMMISSION')->sum('amount'),
            'totalInvestments'     => 0,
            'totalContribution'    => 0,
            'pendingInvestments'   => 0,
            'closeRequests'        => 0,
            'completedInvestments' => 0,
            'withdrawals'          => 0,
            'transactions'         => 0,
        ];

        try {
            $allInvestments = Investment::where('user_id', $user->id)->get();
            if ($allInvestments->isNotEmpty()) {
                $approvedInvestments = $allInvestments->whereNotIn('status', [Investment::STATUS_PENDING, Investment::STATUS_REJECTED]);
                $stats['totalInvestments']     = $approvedInvestments->count();
                $stats['totalContribution']    = (float) $approvedInvestments->sum('amount');
                $stats['pendingInvestments']   = (float) $allInvestments->where('status', Investment::STATUS_PENDING)->sum('amount');
                $stats['completedInvestments'] = $allInvestments->where('status', Investment::STATUS_COMPLETED)->count();
                $stats['closeRequests']        = $allInvestments->where('status', Investment::STATUS_CLOSE_REQUEST)->count();
                $stats['transactions']         = $allInvestments->count();
            }
        } catch (\Exception $e) {
            // Stats remain defaults
        }

        return view('admin.users.details', compact('user', 'stats'));
    }

    public function update(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        if ($request->filled('name')) $user->name = $request->name;

        if ($request->filled('email'))   $user->email   = $request->email;
        if ($request->filled('phone'))   $user->phone   = $request->phone;
        if ($request->filled('address')) $user->address = $request->address;
        if ($request->filled('city'))    $user->city    = $request->city;
        if ($request->filled('state'))   $user->state   = $request->state;
        if ($request->filled('zip'))     $user->zip     = $request->zip;
        if ($request->filled('country')) $user->country = $request->country;

        $user->email_verified_at = $request->input('email_verified') ? now() : null;
        $user->mobile_verified   = (bool) $request->input('mobile_verified', 0);
        $user->two_fa            = (bool) $request->input('two_fa', 0);

        if ($request->filled('kyc_status')) {
            $user->kyc_status = strtoupper($request->kyc_status);
        }

        $user->save();

        return back()->with('success', 'User profile updated successfully.');
    }

    public function updatePassword(Request $request, $username)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'User password updated successfully.');
    }

    public function ban($username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();
        $user->is_active = !$user->is_active;
        $user->save();

        $msg = $user->is_active ? 'User has been unbanned.' : 'User has been banned.';
        return back()->with('success', $msg);
    }

    public function becomeAgent($username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();
        
        if ($user->account_type === 'Root Distributor') {
            return back()->with('error', 'Root Distributors cannot be converted to Agents.');
        }

        $user->account_type = 'Agent';
        $user->save();

        return back()->with('success', 'User has been upgraded to Agent.');
    }

    public function notify(Request $request, $username)
    {
        $request->validate(['title' => 'required|string', 'message' => 'required|string']);

        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $user->notify(new \App\Notifications\GenericNotification($request->title, $request->message));

        return back()->with('success', 'Notification sent successfully.');
    }

    public function impersonate($username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        \Illuminate\Support\Facades\Auth::guard('web')->login($user);

        return redirect()->route('user.dashboard')->with('success', 'You are now impersonating ' . $user->username . '.');
    }

    public function searchApi(Request $request)
    {
        $search = $request->query('search');
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('referral_code', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function storeInvestment(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'trx_id' => 'required|string|max:255|unique:investments,trx_id',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('proofs', 'public');

        Investment::create([
            'user_id' => $user->id,
            'trx_id' => $request->trx_id,
            'amount' => $request->amount,
            'type' => 'manual',
            'status' => Investment::STATUS_PENDING,
            'payment_proof' => $path,
        ]);

        $user->notify(new \App\Notifications\GenericNotification(
            'Investment Created',
            'An investment of ' . format_currency($request->amount) . ' has been created on your account by the admin. It is currently pending review.',
            'ph-trend-up'
        ));

        return back()->with('success', 'Investment created successfully for ' . $user->username . '. It is pending review.');
    }

    public function storeKyc(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $request->validate([
            'document_type' => 'required|string|max:100',
            'document_number' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'document_front_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document_back_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $kyc = Kyc::where('user_id', $user->id)->first() ?? new Kyc(['user_id' => $user->id]);
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

        return back()->with('success', 'KYC details submitted successfully for ' . $user->username . '. It is pending review.');
    }

    public function storeNominee(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'identity_front_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identity_back_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $nominee = Nominee::where('user_id', $user->id)->first() ?? new Nominee(['user_id' => $user->id]);
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

        return back()->with('success', 'Nominee details submitted successfully for ' . $user->username . '. It is pending review.');
    }

    public function storeBank(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'upi_id' => 'nullable|string|max:255',
            'upi_number' => 'nullable|string|max:255',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bank = BankDetail::where('user_id', $user->id)->first() ?? new BankDetail(['user_id' => $user->id]);
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

        return back()->with('success', 'Bank details submitted successfully for ' . $user->username . '. It is pending review.');
    }

    // ==========================================
    // INVESTMENT MANAGEMENT LOGIC
    // ==========================================

    public function allInvestmentsView()
    {
        return view('admin.investments.index');
    }

    public function allInvestmentsApi(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Investment::with('user');

        if ($status && $status !== 'all') {
            if ($status === 'close-requests') {
                $query->where('status', 'CLOSE_REQUEST');
            } else {
                $query->where('status', strtoupper($status));
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('trx_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $investments = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $investments->items(),
            'meta' => [
                'total' => $investments->total(),
                'page' => $investments->currentPage(),
                'limit' => $investments->perPage(),
                'totalPages' => $investments->lastPage(),
            ]
        ]);
    }

    public function investmentDetailsView($trxId)
    {
        $investment = Investment::where('trx_id', $trxId)->with('user')->firstOrFail();
        return view('admin.investments.details', compact('investment'));
    }

    public function updateInvestmentStatusApi(Request $request, $trxId)
    {
        $request->validate([
            'status' => ['required', 'string']
        ]);

        $status = strtoupper($request->status);
        $investment = Investment::where('trx_id', $trxId)->firstOrFail();
        $user = User::findOrFail($investment->user_id);

        if ($investment->status === $status) {
            return response()->json([
                'success' => false,
                'message' => 'Investment is already in this status'
            ], 400);
        }

        if ($status === 'ACTIVE' && $investment->status === 'PENDING') {
            $user->investment_balance = ($user->investment_balance ?? 0) + $investment->amount;
            $user->save();
        }

        if ($status === 'CLOSED') {
            if ($investment->status === 'ACTIVE' || $investment->status === 'CLOSE_REQUEST') {
                $user->investment_balance = max(($user->investment_balance ?? 0) - $investment->amount, 0);
            }
            $user->wallet_balance = ($user->wallet_balance ?? 0) + $investment->amount;
            $user->save();
        }

        $investment->status = $status;
        $investment->save();

        return response()->json([
            'success' => true,
            'message' => 'Investment status updated successfully.',
            'data' => $investment
        ]);
    }

    public function storeContract(Request $request, $username)
    {
        $user = User::where('username', $username)->orWhere('id', $username)->firstOrFail();

        $request->validate([
            'contract_date' => 'nullable|date',
            'contract_notify_date' => 'nullable|date|before_or_equal:contract_date',
            'contract_message' => 'nullable|string',
        ], [
            'contract_notify_date.before_or_equal' => 'The notify date cannot be after the contract date.'
        ]);

        $user->contract_date = $request->contract_date;
        $user->contract_notify_date = $request->contract_notify_date;
        $user->contract_message = $request->contract_message;
        $user->save();

        return back()->with('success', 'Contract details updated successfully.');
    }
}
