<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$words = ['مرحبا', 'شكرا', 'please', 'thankyou', 'صباح الخير', 'see_you_later'];
foreach ($words as $w) {
    $seq = SignAsset::mapTextToSequence($w, 'ar');
    echo "== $w ==\n";
    print_r($seq);
    echo "\n";
}
