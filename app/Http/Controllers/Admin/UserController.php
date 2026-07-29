<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
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
            'closeRequests'        => 0,
            'completedInvestments' => 0,
            'withdrawals'          => 0,
            'transactions'         => 0,
        ];

        try {
            // Count their own investments
            $allInvestments = Investment::where('user_id', $user->id)->get();
            if ($allInvestments->isNotEmpty()) {
                $stats['totalInvestments']     = $allInvestments->count();
                $stats['totalContribution']    = (float) $allInvestments->sum('amount');
                $stats['completedInvestments'] = $allInvestments->where('status', 'COMPLETED')->count();
                $stats['closeRequests']        = $allInvestments->where('status', 'CLOSE_REQUEST')->count();
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

        $firstName = $request->input('firstName', '');
        $lastName  = $request->input('lastName', '');
        if ($firstName || $lastName) {
            $user->name = trim("$firstName $lastName");
        }

        if ($request->filled('email'))   $user->email   = $request->email;
        if ($request->filled('mobile'))  $user->mobile  = $request->mobile;
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
}
