<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$lang = $argv[1] ?? 'ar';
$category = $argv[2] ?? 'words';

$signs = SignAsset::where('language', $lang)->where('active', true)->where('category', $category)->orderBy('text')->get();

echo "Language={$lang} Category={$category} Count=" . $signs->count() . "\n";
foreach ($signs as $s) {
    echo "- key={$s->key} text={$s->text} src={$s->src}\n";
}
