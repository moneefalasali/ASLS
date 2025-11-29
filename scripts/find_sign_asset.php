<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$words = ['مرحبا', 'نعم', 'شكرا', 'hello', 'A'];
foreach ($words as $w) {
    $found = SignAsset::where('key', $w)->orWhere('text', $w)->get();
    echo "Searching for: $w => found: " . $found->count() . "\n";
    foreach ($found as $f) {
        echo "  id={$f->id}, key={$f->key}, lang={$f->language}, src={$f->src}, category={$f->category}\n";
    }
}
