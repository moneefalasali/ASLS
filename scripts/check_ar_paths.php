<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('sign_assets')->select('id','key','src')->where('language','ar')->get();
$missing = [];
foreach ($rows as $r) {
    $src = $r->src;
    if (strpos($src,'ar_') !== false) {
        // build full path
        $path = __DIR__ . '/../public/storage/' . ltrim($src, '/');
        if (!file_exists($path)) {
            $missing[] = [$r->id, $r->key, $r->src, $path];
        }
    }
}
if (empty($missing)) {
    echo "No missing ar files referenced by DB.\n";
} else {
    foreach ($missing as $m) {
        echo "Missing file for id={$m[0]} key={$m[1]} src={$m[2]} expectedPath={$m[3]}\n";
    }
}
