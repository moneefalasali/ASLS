<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$keys = ['مرحبا','marhaba','marhaba','مرحباً','شكرًا','شكرا','thankyou','thank','please','shukran','see_you_later','اراك_لاحقا'];
foreach ($keys as $k) {
    $asset = SignAsset::where('key', $k)->orWhere('text', $k)->first();
    echo "Query: $k => ";
    if ($asset) {
        echo "FOUND src={$asset->src} key={$asset->key} text={$asset->text} lang={$asset->language}\n";
    } else {
        echo "NOT FOUND\n";
    }
}
