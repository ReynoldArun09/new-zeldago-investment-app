<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
$investment = new \App\Models\Investment();
$investment->user_id = $user->id;
$investment->trx_id = 'INV-' . strtoupper(uniqid());
$investment->amount = 10000;
$investment->status = \App\Models\Investment::STATUS_ACTIVE;
$investment->roi_cycle_start_date = now();
$investment->created_at = now();
$investment->updated_at = now();
$investment->save();

$roi = new \App\Models\RoiLog();
$roi->trx_id = 'ROI-' . strtoupper(uniqid());
$roi->investment_id = $investment->id;
$roi->user_id = $user->id;
$roi->amount = 1000;
$roi->rate = 10;
$roi->status = 'credited';
$roi->created_at = now();
$roi->updated_at = now();
$roi->save();

echo "Seeded. Testing dashboard render...\n";
ob_start();
$request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$response = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
echo "Response status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
ob_end_clean();
echo "Length: " . strlen($content) . "\n";
