<?php
// CLI script to seed test sign assets (same as dev route)
// Run: php scripts/seed_test_signs_cli.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;

$testSigns = [
    ['key' => 'مرحبا', 'text' => 'مرحبا', 'language' => 'ar', 'category' => 'greetings'],
    ['key' => 'شكرا', 'text' => 'شكرا', 'language' => 'ar', 'category' => 'greetings'],
    ['key' => 'hello', 'text' => 'hello', 'language' => 'en', 'category' => 'greetings'],
    ['key' => 'thank', 'text' => 'thank you', 'language' => 'en', 'category' => 'greetings']
];

foreach ($testSigns as $sign) {
    SignAsset::updateOrCreate(
        ['key' => $sign['key'], 'language' => $sign['language']],
        array_merge($sign, [
            'type' => 'image',
            'src' => '/storage/signs/placeholder.png',
            'active' => true
        ])
    );
}

echo "OK: Test signs seeded.\n";
