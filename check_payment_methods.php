<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Payment Methods ===" . PHP_EOL . PHP_EOL;

echo "Payments with Payment Method IDs:" . PHP_EOL;
echo str_repeat('-', 70) . PHP_EOL;

$payments = \Modules\Graveyard\Models\Payment::select('id', 'payment_reference', 'payment_method_id')
    ->whereNotNull('payment_method_id')
    ->limit(10)
    ->get();

foreach ($payments as $payment) {
    $pmExists = \Modules\Fund\Models\PaymentMethod::find($payment->payment_method_id);
    $status = $pmExists ? "✓ EXISTS: {$pmExists->name}" : "✗ NOT FOUND";

    echo sprintf(
        "Payment ID: %-5d | Ref: %-20s | PM_ID: %-3d | %s",
        $payment->id,
        $payment->payment_reference,
        $payment->payment_method_id,
        $status
    ) . PHP_EOL;
}

echo PHP_EOL . "Available Payment Methods in Database:" . PHP_EOL;
echo str_repeat('-', 70) . PHP_EOL;

$paymentMethods = \Modules\Fund\Models\PaymentMethod::select('id', 'name')->get();

foreach ($paymentMethods as $pm) {
    echo sprintf("ID: %-3d | Name: %s", $pm->id, $pm->name) . PHP_EOL;
}

echo PHP_EOL . "=== Check Complete ===" . PHP_EOL;
