<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blade = $app->make('blade.compiler');
$salesEmp = \App\Models\Employee::where('role', 'sales')->first() ?? \App\Models\Employee::first();
\Illuminate\Support\Facades\Auth::guard('web')->login($salesEmp);

try {
    $req = Illuminate\Http\Request::create('/sales/dashboard', 'GET');
    $res = $app->handle($req);
    echo "Status: " . $res->getStatusCode() . "\n";
    if ($res->getStatusCode() >= 400) {
        if ($res->exception) {
            echo "Exception: " . $res->exception->getMessage() . "\n";
            echo "In file: " . $res->exception->getFile() . ":" . $res->exception->getLine() . "\n";
            echo $res->exception->getTraceAsString();
        }
    }
} catch (\Throwable $e) {
    echo "Caught: " . $e->getMessage() . "\n";
    echo "In: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
