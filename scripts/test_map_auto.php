<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\SignAsset;

// test english
$seq = SignAsset::mapTextToSequence('A');
print_r($seq);

// test arabic
$seq2 = SignAsset::mapTextToSequence('أ');
print_r($seq2);

$seq3 = SignAsset::mapTextToSequence('ا');
print_r($seq3);
print_r($seq);
