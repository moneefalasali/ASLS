<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('sign_assets')->select('src')->where('language','ar')->distinct()->get()->pluck('src')->toArray();
foreach ($rows as $r) {
    if (strpos($r,'ar_') !== false) echo $r . "\n";
}
