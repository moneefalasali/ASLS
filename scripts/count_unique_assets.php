<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

echo 'EN unique letters: ' . SignAsset::where('language','en')->where('category','letters')->distinct('key')->count() . "\n";
echo 'AR unique letters: ' . SignAsset::where('language','ar')->where('category','letters')->distinct('key')->count() . "\n";
echo 'EN words: ' . SignAsset::where('language','en')->where('category','words')->count() . "\n";
echo 'AR words: ' . SignAsset::where('language','ar')->where('category','words')->count() . "\n";
