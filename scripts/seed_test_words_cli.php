<?php
// Seed some 'words' category signs for Arabic and English
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$testWords = [
    ['key' => 'كتاب', 'text' => 'كتاب', 'language' => 'ar', 'category' => 'words'],
    ['key' => 'قلم', 'text' => 'قلم', 'language' => 'ar', 'category' => 'words'],
    ['key' => 'ماء', 'text' => 'ماء', 'language' => 'ar', 'category' => 'words'],
    ['key' => 'book', 'text' => 'book', 'language' => 'en', 'category' => 'words'],
    ['key' => 'pen', 'text' => 'pen', 'language' => 'en', 'category' => 'words'],
    ['key' => 'water', 'text' => 'water', 'language' => 'en', 'category' => 'words']
];

foreach ($testWords as $sign) {
    SignAsset::updateOrCreate(
        ['key' => $sign['key'], 'language' => $sign['language']],
        array_merge($sign, [
            'type' => 'image',
            'src' => '/storage/signs/placeholder.png',
            'active' => true
        ])
    );
}

echo "OK: Test words seeded.\n";
