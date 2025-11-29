<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

try {
    $seq = SignAsset::mapTextToSequence('مرحبا', 'ar');
    echo "Sequence count: " . count($seq) . "\n";
    print_r($seq);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

$seq2 = SignAsset::mapTextToSequence('نعم', 'ar');
echo "Sequence count for نعم: " . count($seq2) . "\n";
print_r($seq2);

$seq3 = SignAsset::mapTextToSequence('hello', 'en');
echo "Sequence count for hello: " . count($seq3) . "\n";
print_r($seq3);

$seq4 = SignAsset::mapTextToSequence('A', 'en');
echo "Sequence count for A: " . count($seq4) . "\n";
print_r($seq4);
