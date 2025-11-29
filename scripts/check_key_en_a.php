<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$asset = SignAsset::where('language','en')->where('key','en_a')->first();
if ($asset) {
    echo "EN_A found: {$asset->key} {$asset->src} {$asset->category}\n";
} else {
    echo "EN_A not found\n";
}

$asset2 = SignAsset::where('language','en')->where('key','a')->first();
if ($asset2) {
    echo "a found: {$asset2->key} {$asset2->src} {$asset2->category}\n";
}
