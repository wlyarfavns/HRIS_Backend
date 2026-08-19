<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $u = \App\Models\User::role('finance')->first();
    \Illuminate\Support\Facades\Auth::login($u);

    $payroll = \App\Models\Payroll::first();
    $request = Illuminate\Http\Request::create('/finance/disbursement/'.$payroll->id.'/slip?export=pdf', 'GET');
    
    $httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $httpKernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Content-Disposition: " . $response->headers->get('Content-Disposition') . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
}
