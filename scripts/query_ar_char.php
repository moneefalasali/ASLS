<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\SignAsset;

$chars = ['ا','أ','إ','آ','ب','ت','ث'];
foreach ($chars as $c) {
    $hex = strtolower(bin2hex(mb_convert_encoding($c,'UTF-8')));
    $key1 = $c;
    $key2 = 'ar_' . $hex;
    $row = SignAsset::where('language','ar')->where(function($q) use ($key1, $key2){
        $q->where('key',$key1)->orWhere('key',$key2);
    })->first();
    if ($row) {
        echo "Char: $c -> Found key={$row->key}, src={$row->src}\n";
    } else {
        echo "Char: $c -> NOT FOUND (tried '$key1' and '$key2')\n";
    }
}
