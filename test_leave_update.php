<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\LeaveController;

$request = Request::create('/admin/leaves/balance/update', 'POST', [
    'employee_id' => 'all',
    'year' => date('Y'),
    'casual_leave_total' => 12,
    'casual_leave_used' => 0,
    'sick_leave_total' => 12,
    'sick_leave_used' => 0,
    'annual_leave_total' => 15,
    'annual_leave_used' => 0,
]);

$controller = app()->make(LeaveController::class);
try {
    $response = $controller->updateBalance($request);
    echo "Response: " . get_class($response) . "\n";
    if (method_exists($response, 'getSession')) {
        echo "Session Success: " . json_encode($response->getSession()->get('success')) . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
