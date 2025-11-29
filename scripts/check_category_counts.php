<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

echo "Non-null categories: " . SignAsset::whereNotNull('category')->count() . "\n";
echo "Distinct categories: \n";
print_r(SignAsset::select('category', \DB::raw('count(*) as c'))->groupBy('category')->pluck('c','category')->toArray());

echo "Null category count: " . SignAsset::whereNull('category')->count() . "\n";
