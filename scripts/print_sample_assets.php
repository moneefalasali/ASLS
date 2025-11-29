<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$examples = SignAsset::orderBy('id')->limit(100)->get(['id','key','language','src','category']);
foreach ($examples as $row) {
    echo sprintf("%3d %s %s %s %s\n", $row->id, $row->language, $row->key, $row->src, $row->category ?? 'null');
}
