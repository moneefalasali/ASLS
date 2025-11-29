<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;
use Illuminate\Support\Facades\DB;

$dups = DB::table('sign_assets')
    ->select('language','category','key', DB::raw('count(*) as c'))
    ->groupBy('language','category','key')
    ->havingRaw('count(*) > 1')
    ->get();

foreach ($dups as $d) {
    echo "{$d->language} {$d->category} {$d->key} -> {$d->c}\n";
}

$topKeys = DB::table('sign_assets')
    ->select('key', DB::raw('count(*) as c'))
    ->groupBy('key')
    ->orderBy('c', 'desc')
    ->limit(30)
    ->get();

foreach ($topKeys as $tk) {
    echo "Key: {$tk->key} count: {$tk->c}\n";
}
