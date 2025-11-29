<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

try {
    $total = SignAsset::count();
    $lettersEn = SignAsset::where('language', 'en')->where('category', 'letters')->count();
    $lettersAr = SignAsset::where('language', 'ar')->where('category', 'letters')->count();
    $wordsEn = SignAsset::where('language', 'en')->where('category', 'words')->count();
    $wordsAr = SignAsset::where('language', 'ar')->where('category', 'words')->count();

    echo "Total signs: $total\n";
    echo "English letter signs (category=letters): $lettersEn\n";
    echo "Arabic letter signs (category=letters): $lettersAr\n";
    echo "English word signs (category=words): $wordsEn\n";
    echo "Arabic word signs (category=words): $wordsAr\n";

    // show sample missing letters or duplicates
    $enLetters = SignAsset::where('language','en')->where('category','letters')->orderBy('key')->get(['id','key','src'])->toArray();
    echo "\nSample EN letters (first 10):\n";
    foreach (array_slice($enLetters,0,10) as $row) {
        echo $row['key'] . ' => ' . $row['src'] . "\n";
    }

    $arWords = SignAsset::where('language','ar')->where('category','words')->orderBy('key')->get(['id','key','src'])->toArray();
    echo "\nSample AR words (first 10):\n";
    foreach (array_slice($arWords,0,10) as $row) {
        echo $row['key'] . ' => ' . $row['src'] . "\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
