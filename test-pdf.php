<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $payroll = App\Models\Payroll::with(['employee.department', 'employee.position', 'details.salaryComponent', 'payrollBatch'])->first();
    $earnings = collect([['label' => 'Gaji Pokok', 'amount' => (float) $payroll->basic_salary]]);
    $deductions = collect([]);
    $totalEarnings = 1000;
    $totalDeductions = 0;

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mobile.payroll.slip-pdf', compact('payroll', 'earnings', 'deductions', 'totalEarnings', 'totalDeductions'));
    $pdf->output();
    echo "PDF generated successfully.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
