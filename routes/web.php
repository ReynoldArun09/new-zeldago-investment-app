<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController as UserAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [UserAuthController::class, 'login']);

    Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard');
    
    // User Investments
    Route::prefix('investments')->name('user.investments.')->group(function () {
        Route::get('/new', [\App\Http\Controllers\User\InvestmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\User\InvestmentController::class, 'store'])->name('store');
        Route::get('/active', [\App\Http\Controllers\User\InvestmentController::class, 'active'])->name('active');
        Route::get('/closed', [\App\Http\Controllers\User\InvestmentController::class, 'closed'])->name('closed');
        Route::post('/{id}/close', [\App\Http\Controllers\User\InvestmentController::class, 'closeRequest'])->name('close');
    });

    // User Network
    Route::prefix('network')->name('user.network.')->group(function () {
        Route::get('/referrals', [\App\Http\Controllers\User\NetworkController::class, 'referrals'])->name('referrals');
        Route::get('/genealogy', [\App\Http\Controllers\User\NetworkController::class, 'genealogy'])->name('genealogy');
    });

    // User Finance
    Route::prefix('finance')->name('user.finance.')->group(function () {
        Route::get('/transactions', [\App\Http\Controllers\User\FinanceController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [\App\Http\Controllers\User\FinanceController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/withdrawals', [\App\Http\Controllers\User\FinanceController::class, 'submitWithdrawal'])->name('withdrawals.submit');
    });
    
    // User Settings
    Route::prefix('settings')->name('user.settings.')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\User\SettingsController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\User\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::get('/password', [\App\Http\Controllers\User\SettingsController::class, 'password'])->name('password');
        Route::post('/password', [\App\Http\Controllers\User\SettingsController::class, 'updatePassword'])->name('password.update');
    });

    // User Verification
    Route::prefix('verification')->name('user.verification.')->group(function () {
        Route::get('/kyc', [\App\Http\Controllers\User\VerificationController::class, 'kyc'])->name('kyc');
        Route::post('/kyc', [\App\Http\Controllers\User\VerificationController::class, 'submitKyc'])->name('kyc.submit');
        Route::get('/nominee', [\App\Http\Controllers\User\VerificationController::class, 'nominee'])->name('nominee');
        Route::post('/nominee', [\App\Http\Controllers\User\VerificationController::class, 'submitNominee'])->name('nominee.submit');
    });

    // User Notifications
    Route::prefix('notifications')->name('user.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [\App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('mark-read');
    });
    // User Support Tickets
    Route::prefix('support')->name('user.support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\TicketController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\User\TicketController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\User\TicketController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\User\TicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [\App\Http\Controllers\User\TicketController::class, 'reply'])->name('reply');
    });
    
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
});

use App\Http\Controllers\Admin\AuthController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
        
    });

    Route::middleware('auth:admin')->group(function () {
        // Redirect /admin to /admin/dashboard when logged in
        Route::get('/', function() { return redirect()->route('admin.dashboard'); });
        
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profile & Password routes
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::get('password', [AuthController::class, 'password'])->name('password');
        Route::post('password', [AuthController::class, 'updatePassword'])->name('password.update');

        // User Management Routes
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('users/{username}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.details');
        Route::put('users/{username}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::put('users/{username}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
        Route::post('users/{username}/notify', [\App\Http\Controllers\Admin\UserController::class, 'notify'])->name('users.notification');
        Route::post('users/{username}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');

        // Settings routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('admin',      fn() => view('admin.settings.admin-settings'))->name('admin');

            // Admin Sub-pages
            Route::get('admin/logo', fn() => view('admin.settings.logo'))->name('logo');
            Route::post('admin/logo', [\App\Http\Controllers\Admin\SettingsController::class, 'updateLogoFavicon'])->name('logo.update');

            Route::get('admin/seo', fn() => view('admin.settings.seo'))->name('seo');
            Route::post('admin/seo', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSeo'])->name('seo.update');

            Route::get('admin/theme', fn() => view('admin.settings.theme'))->name('theme');
            Route::post('admin/theme', [\App\Http\Controllers\Admin\SettingsController::class, 'updateTheme'])->name('theme.update');
            
            Route::get('admin/currency', fn() => view('admin.settings.currency'))->name('currency');
            Route::post('admin/currency', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCurrency'])->name('currency.update');
            Route::get('admin/roi', fn() => view('admin.settings.roi'))->name('roi');
            Route::post('admin/roi', [\App\Http\Controllers\Admin\SettingsController::class, 'updateRoi'])->name('roi.update');
            

            Route::get('admin/commission', [\App\Http\Controllers\Admin\CommissionSettingController::class, 'edit'])->name('commission');
            Route::post('admin/commission', [\App\Http\Controllers\Admin\CommissionSettingController::class, 'update'])->name('commission.update');

            
            // API endpoints for updating/fetching
            Route::get('api/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'get'])->name('api.get');
            Route::post('api/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('api.update');
        });

        Route::post('notifications/mark-all-read', function () {
            Auth::guard('admin')->user()->unreadNotifications->markAsRead();
            return back();
        })->name('notifications.mark-all-read');

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('api/users/search', [\App\Http\Controllers\Admin\UserController::class, 'searchApi'])->name('api.users.search');
        

        // ROI Management
        Route::prefix('roi')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RoiController::class, 'index'])->name('roi.index');
            Route::get('/pending', [\App\Http\Controllers\Admin\RoiController::class, 'pending'])->name('roi.pending');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\RoiController::class, 'approve'])->name('roi.approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\RoiController::class, 'reject'])->name('roi.reject');
        });

        // Investment Management
        Route::prefix('investments')->name('investments.')->group(function () {
            Route::get('/details/{id}', [\App\Http\Controllers\Admin\InvestmentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\InvestmentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\InvestmentController::class, 'reject'])->name('reject');
            Route::get('/{status?}', [\App\Http\Controllers\Admin\InvestmentController::class, 'index'])->name('index');
        });

        // Reports Management
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/investment', [\App\Http\Controllers\Admin\ReportController::class, 'investmentReport'])->name('investment');
            Route::get('/roi', [\App\Http\Controllers\Admin\ReportController::class, 'roiReport'])->name('roi');
            Route::get('/commissions', [\App\Http\Controllers\Admin\ReportController::class, 'commissionsReport'])->name('commissions');
            Route::get('/withdrawals', [\App\Http\Controllers\Admin\ReportController::class, 'withdrawalsReport'])->name('withdrawals');
        });
        
        Route::get('/commission-log', [\App\Http\Controllers\Admin\CommissionLogController::class, 'index'])->name('commission-log');

        Route::get('api/agents/search', [\App\Http\Controllers\Admin\UserController::class, 'searchAgents'])->name('api.agents.search');
        Route::post('api/agents/{id}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetAgentPassword'])->name('api.agents.reset-password');
        Route::get('api/investors/search', [\App\Http\Controllers\Admin\UserController::class, 'searchInvestors'])->name('api.investors.search');
        Route::post('api/investors/{id}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetInvestorPassword'])->name('api.investors.reset-password');
        // Support Tickets Management
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('reply');
            Route::post('/{id}/close', [\App\Http\Controllers\Admin\SupportTicketController::class, 'close'])->name('close');
        });

        // Verification Routes
        Route::prefix('verification')->name('verification.')->group(function () {
            Route::get('kyc/review/{id}', [\App\Http\Controllers\Admin\VerificationController::class, 'kycReview'])->name('kyc.review');
            Route::get('kyc/{status?}', [\App\Http\Controllers\Admin\VerificationController::class, 'kycList'])->name('kyc');
            Route::post('kyc/{id}/status', [\App\Http\Controllers\Admin\VerificationController::class, 'kycUpdate'])->name('kyc.status');
            
            Route::get('nominee/review/{id}', [\App\Http\Controllers\Admin\VerificationController::class, 'nomineeReview'])->name('nominee.review');
            Route::get('nominee/{status?}', [\App\Http\Controllers\Admin\VerificationController::class, 'nomineeList'])->name('nominee');
            Route::post('nominee/{id}/status', [\App\Http\Controllers\Admin\VerificationController::class, 'nomineeUpdate'])->name('nominee.status');
        });
    });
});
