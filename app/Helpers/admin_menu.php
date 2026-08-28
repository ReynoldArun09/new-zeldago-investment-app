<?php

use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| Admin Sidebar Menu Helpers
|--------------------------------------------------------------------------
*/

function get_setting(string $key, $default = null)
{
    try {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    } catch (\Exception $e) {
        return $default;
    }
}

function format_currency($amount)
{
    $symbol = get_setting('currency_symbol', 'Rs');
    $code = get_setting('currency_code', 'INR');
    
    return $symbol . ' ' . number_format((float)$amount, 2);
}

function default_currency()
{
    return get_setting('currency_code', 'INR');
}

function admin_sidebar_menu(): array
{
    return [
        [
            'label' => 'Dashboard',
            'icon'  => 'home',
            'href'  => '/admin/dashboard',
        ],

        [
            'label'    => 'User Management',
            'icon'     => 'users',
            'children' => [
                ['label' => 'All Investors',       'href' => '/admin/users/investors','icon' => 'user'],
                ['label' => 'All Agents',          'href' => '/admin/users/agents',   'icon' => 'briefcase'],
                ['label' => 'All Users',           'href' => '/admin/users',              'icon' => 'users'],
            ],
        ],

        [
            'label'    => 'Investments',
            'icon'     => 'trending-up',
            'children' => [
                ['label' => 'All Investments',       'href' => '/admin/investments',               'icon' => 'list'],
                ['label' => 'Pending Investments',   'href' => '/admin/investments/pending',       'icon' => 'hourglass'],
                ['label' => 'Active Investments',    'href' => '/admin/investments/active',        'icon' => 'check-circle'],
                ['label' => 'Completed Investments', 'href' => '/admin/investments/completed',     'icon' => 'check-square'],
                ['label' => 'Closed Investments',    'href' => '/admin/investments/closed',        'icon' => 'x-circle'],
                ['label' => 'Close Requests',        'href' => '/admin/investments/close-requests','icon' => 'clock'],
            ],
        ],

        [
            'label'    => 'ROI Management',
            'icon'     => 'percent',
            'children' => [
                ['label' => 'All ROI',             'href' => '/admin/roi',                           'icon' => 'list'],
                ['label' => 'Pending Requests',    'href' => '/admin/roi/pending',                   'icon' => 'clock'],
                ['label' => 'Processing Requests', 'href' => '/admin/roi/processing',                'icon' => 'settings'],
            ],
        ],

        [
            'label'    => 'ROI Management (Old)',
            'icon'     => 'percent',
            'children' => [
                ['label' => 'All Old ROI',         'href' => '/admin/old-roi',                       'icon' => 'list'],
                ['label' => 'Pending Requests',    'href' => '/admin/old-roi/pending',               'icon' => 'clock'],
                ['label' => 'Processing Requests', 'href' => '/admin/old-roi/processing',            'icon' => 'settings'],
            ],
        ],

        [
            'label'    => 'Agent Withdrawal',
            'icon'     => 'landmark',
            'children' => [
                ['label' => 'All Withdrawals',      'href' => '/admin/withdrawals',                  'icon' => 'list'],
                ['label' => 'Pending Requests',     'href' => '/admin/withdrawals/pending',          'icon' => 'clock'],
            ],
        ],

        [
            'label'    => 'KYC Management',
            'icon'     => 'shield-check',
            'children' => [
                ['label' => 'All KYC',             'href' => '/admin/verification/kyc',              'icon' => 'list'],
                ['label' => 'Pending KYC',         'href' => '/admin/verification/kyc/pending',      'icon' => 'clock'],
                ['label' => 'Approved KYC',        'href' => '/admin/verification/kyc/approved',     'icon' => 'check-circle'],
                ['label' => 'Rejected KYC',        'href' => '/admin/verification/kyc/rejected',     'icon' => 'x-circle'],
            ],
        ],

        [
            'label'    => 'Nominee Management',
            'icon'     => 'user-plus',
            'children' => [
                ['label' => 'All Nominee',         'href' => '/admin/verification/nominee',          'icon' => 'list'],
                ['label' => 'Pending Nominee',     'href' => '/admin/verification/nominee/pending',  'icon' => 'clock'],
                ['label' => 'Approved Nominee',    'href' => '/admin/verification/nominee/approved', 'icon' => 'check-circle'],
                ['label' => 'Rejected Nominee',    'href' => '/admin/verification/nominee/rejected', 'icon' => 'x-circle'],
            ],
        ],

        [
            'label'    => 'Bank Details',
            'icon'     => 'credit-card',
            'children' => [
                ['label' => 'All Bank Details',    'href' => '/admin/verification/bank',          'icon' => 'list'],
                ['label' => 'Pending Bank Details','href' => '/admin/verification/bank/pending',  'icon' => 'clock'],
                ['label' => 'Approved Bank Details','href' => '/admin/verification/bank/approved', 'icon' => 'check-circle'],
                ['label' => 'Rejected Bank Details','href' => '/admin/verification/bank/rejected', 'icon' => 'x-circle'],
            ],
        ],

        [
            'label'    => 'Reports',
            'icon'     => 'pie-chart',
            'children' => [
                ['label' => 'Investment Report',   'href' => '/admin/reports/investment',            'icon' => 'trending-up'],
                ['label' => 'ROI Report',          'href' => '/admin/reports/roi',                   'icon' => 'percent'],
                ['label' => 'Commissions Report',  'href' => '/admin/reports/commissions',           'icon' => 'coins'],
                ['label' => 'Withdrawals Report',  'href' => '/admin/reports/withdrawals',           'icon' => 'landmark'],
            ],
        ],

        [
            'label'    => 'Support Tickets',
            'icon'     => 'mail',
            'href'     => '/admin/support',
        ],

        [
            'label'    => 'Commission Log',
            'icon'     => 'coins',
            'href'     => '/admin/commission-log',
        ],

        [
            'label'    => 'Notifications',
            'icon'     => 'bell',
            'href'     => '/admin/notifications',
        ],

        [
            'label'    => 'System Settings',
            'icon'     => 'settings',
            'children' => [
                ['label' => 'Admin Settings',      'href' => '/admin/settings/admin',      'icon' => 'wrench'],
            ],
        ],
    ];
}
