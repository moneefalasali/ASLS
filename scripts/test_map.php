<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
// Boot the app to use Eloquent
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$seq = SignAsset::mapTextToSequence('A', 'en');
print_r($seq);
