<?php

use App\Models\RentalOrderMailLog;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$log = RentalOrderMailLog::query()
    ->where('status', 'failed')
    ->latest('id')
    ->first(['id', 'to_email', 'type', 'error_message', 'attempted_at', 'created_at']);

if (!$log) {
    echo "NO_FAILED\n";
    exit(0);
}

echo "ID: {$log->id}\n";
echo "To: {$log->to_email}\n";
echo "Type: {$log->type}\n";
echo "Attempted: " . ($log->attempted_at?->format('c') ?? '-') . "\n";
echo "Error: " . ($log->error_message ?? '-') . "\n";

