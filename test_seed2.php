<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::guard('admin')->login(\App\Models\Admin::first());
echo "Seeded earlier. Testing dashboard render...\n";
ob_start();
$request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$response = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
echo "Response status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
ob_end_clean();
echo "Length: " . strlen($content) . "\n";
if (strpos($content, 'ERR_') !== false || strlen($content) < 1000) { echo $content; }
