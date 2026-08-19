<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::role('finance')->first();
\Illuminate\Support\Facades\Auth::login($u);

$req = Illuminate\Http\Request::create('/finance/reimbursement', 'GET');
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$res = $httpKernel->handle($req);
file_put_contents('test.html', $res->getContent());
