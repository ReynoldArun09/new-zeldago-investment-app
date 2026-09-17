<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as UserAuthController;

Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => [
                'database/migrations/2026_08_13_113956_add_is_old_to_investments_table.php',
                'database/migrations/2026_08_13_123939_add_direct_roi_amount_to_roi_logs_table.php'
            ],
            '--force' => true
        ]);
        return "Specific migrations executed successfully!<br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Error running migrations: " . $e->getMessage() . "<br>File: " . $e->getFile() . " Line: " . $e->getLine();
    }
});

Route::middleware('guest')->group(function () {
    Route::get('/', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [UserAuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard');
    
    // User Investments
    Route::prefix('investments')->name('user.investments.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\User\InvestmentController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\User\InvestmentController::class, 'store'])->name('store');
        Route::get('/active', [\App\Http\Controllers\User\InvestmentController::class, 'active'])->name('active');
        Route::get('/closed', [\App\Http\Controllers\User\InvestmentController::class, 'closed'])->name('closed');
        Route::post('/{id}/close', [\App\Http\Controllers\User\InvestmentController::class, 'closeRequest'])->name('close');
    });

    // User Network
    Route::prefix('network')->name('user.network.')->group(function () {
        Route::get('/referrals', [\App\Http\Controllers\User\NetworkController::class, 'referrals'])->name('referrals');
        Route::get('/genealogy', [\App\Http\Controllers\User\NetworkController::class, 'genealogy'])->name('genealogy');
        Route::post('/add-investor', [\App\Http\Controllers\User\NetworkController::class, 'addInvestor'])->name('add-investor');
        Route::post('/add-investment', [\App\Http\Controllers\User\NetworkController::class, 'addInvestment'])->name('add-investment');

        Route::get('/investor/{id}/investments', [\App\Http\Controllers\User\NetworkController::class, 'investorInvestments'])->name('investor.investments');
    });

    // User Finance
    Route::prefix('finance')->name('user.finance.')->group(function () {
        Route::get('/transactions/commissions', [\App\Http\Controllers\User\FinanceController::class, 'commissionTransactions'])->name('transactions.commissions');
        Route::get('/transactions/roi', [\App\Http\Controllers\User\FinanceController::class, 'roiTransactions'])->name('transactions.roi');
        Route::get('/transactions/direct-roi', [\App\Http\Controllers\User\FinanceController::class, 'directRoiTransactions'])->name('transactions.direct_roi');
        Route::get('/withdrawals', [\App\Http\Controllers\User\FinanceController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/withdrawals', [\App\Http\Controllers\User\FinanceController::class, 'submitWithdrawal'])->name('withdrawals.submit');
        Route::get('/transfer', [\App\Http\Controllers\User\FinanceController::class, 'transfer'])->name('transfer');
        Route::post('/transfer/search', [\App\Http\Controllers\User\FinanceController::class, 'searchAgent'])->name('transfer.search');
        Route::post('/transfer', [\App\Http\Controllers\User\FinanceController::class, 'submitTransfer'])->name('transfer.submit');
    });
    
    // User Settings
    // Statement Downloads
    Route::get('/statements/download', [\App\Http\Controllers\User\DashboardController::class, 'downloadStatements'])->name('user.statements.download');

    Route::prefix('settings')->name('user.settings.')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\User\SettingsController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\User\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::get('/password', [\App\Http\Controllers\User\SettingsController::class, 'password'])->name('password');
        Route::post('/password', [\App\Http\Controllers\User\SettingsController::class, 'updatePassword'])->name('password.update');
    });

    // User Verification
    Route::prefix('verification')->name('user.verification.')->group(function () {
        Route::get('/kyc', [\App\Http\Controllers\User\VerificationController::class, 'kyc'])->name('kyc');
        Route::post('/kyc', [\App\Http\Controllers\User\VerificationController::class, 'storeKyc'])->name('kyc.store');
        
        Route::get('/nominee', [\App\Http\Controllers\User\VerificationController::class, 'nominee'])->name('nominee');
        Route::post('/nominee', [\App\Http\Controllers\User\VerificationController::class, 'storeNominee'])->name('nominee.store');
        
        Route::get('/bank', [\App\Http\Controllers\User\VerificationController::class, 'bank'])->name('bank');
        Route::post('/bank', [\App\Http\Controllers\User\VerificationController::class, 'storeBank'])->name('bank.store');
        Route::post('/bank/confirm', [\App\Http\Controllers\User\VerificationController::class, 'confirmBank'])->name('bank.confirm');
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
        Route::get('users/investors', [\App\Http\Controllers\Admin\UserController::class, 'investors'])->name('users.investors');
        Route::get('users/agents', [\App\Http\Controllers\Admin\UserController::class, 'agents'])->name('users.agents');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('users/{username}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.details');
        Route::post('users/{username}/investment', [\App\Http\Controllers\Admin\UserController::class, 'storeInvestment'])->name('users.investment.store');
        Route::post('users/{username}/kyc', [\App\Http\Controllers\Admin\UserController::class, 'storeKyc'])->name('users.kyc.store');
        Route::post('users/{username}/nominee', [\App\Http\Controllers\Admin\UserController::class, 'storeNominee'])->name('users.nominee.store');
        Route::post('users/{username}/bank', [\App\Http\Controllers\Admin\UserController::class, 'storeBank'])->name('users.bank.store');
        Route::put('users/{username}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::put('users/{username}/password', [\App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('users.updatePassword');
        Route::put('users/{username}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
        Route::post('users/{username}/notify', [\App\Http\Controllers\Admin\UserController::class, 'notify'])->name('users.notification');
        Route::post('users/{username}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');
        Route::put('users/{username}/become-agent', [\App\Http\Controllers\Admin\UserController::class, 'becomeAgent'])->name('users.become-agent');
        Route::post('users/{username}/contract', [\App\Http\Controllers\Admin\UserController::class, 'storeContract'])->name('users.contract');

        // Settings routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('unlock', [\App\Http\Controllers\Admin\AuthController::class, 'showSettingsUnlockForm'])->name('unlock');
            Route::post('unlock', [\App\Http\Controllers\Admin\AuthController::class, 'unlockSettings'])->name('unlock.submit');

            Route::middleware('admin.settings.lock')->group(function () {
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
            });

            
            // API endpoints for updating/fetching
            Route::get('api/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'get'])->name('api.get');
            Route::post('api/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('api.update');
        });

        Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/history', [\App\Http\Controllers\Admin\NotificationController::class, 'history'])->name('notifications.history');
        Route::post('notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
        Route::post('notifications/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
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
            Route::get('/processing', [\App\Http\Controllers\Admin\RoiController::class, 'processing'])->name('roi.processing');
            Route::post('/{id}/process', [\App\Http\Controllers\Admin\RoiController::class, 'process'])->name('roi.process');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\RoiController::class, 'approve'])->name('roi.approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\RoiController::class, 'reject'])->name('roi.reject');
        });


        // Investment Management
        Route::prefix('investments')->name('investments.')->group(function () {
            Route::get('/export/{status?}', [\App\Http\Controllers\Admin\InvestmentController::class, 'export'])->name('export');
            Route::get('/details/{id}', [\App\Http\Controllers\Admin\InvestmentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\InvestmentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\InvestmentController::class, 'reject'])->name('reject');
            Route::post('/{id}/update-roi', [\App\Http\Controllers\Admin\InvestmentController::class, 'updateRoi'])->name('update-roi');
            Route::get('/{status?}', [\App\Http\Controllers\Admin\InvestmentController::class, 'index'])->name('index');
        });

        // Reports Management
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/investment', [\App\Http\Controllers\Admin\ReportController::class, 'investmentReport'])->name('investment');
            Route::get('/roi', [\App\Http\Controllers\Admin\ReportController::class, 'roiReport'])->name('roi');
            Route::get('/commissions', [\App\Http\Controllers\Admin\ReportController::class, 'commissionsReport'])->name('commissions');
            Route::get('/withdrawals', [\App\Http\Controllers\Admin\ReportController::class, 'withdrawalsReport'])->name('withdrawals');
        });

        // Withdrawal Management
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('index');
            Route::get('/pending', [\App\Http\Controllers\Admin\WithdrawalController::class, 'pending'])->name('pending');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('reject');
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

            Route::get('bank/review/{id}', [\App\Http\Controllers\Admin\VerificationController::class, 'bankReview'])->name('bank.review');
            Route::get('bank/{status?}', [\App\Http\Controllers\Admin\VerificationController::class, 'bankList'])->name('bank');
            Route::post('bank/{id}/status', [\App\Http\Controllers\Admin\VerificationController::class, 'bankUpdate'])->name('bank.status');
        });
    });
});
