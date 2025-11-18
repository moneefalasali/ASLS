<?php
// Scan public/storage/signs/letters and index images into sign_assets table
// Usage: php scripts/index_sign_images.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SignAsset;
use Illuminate\Support\Str;

// Scan both letters and words directories under public/storage/signs
$baseDir = public_path('storage/signs');
if (!is_dir($baseDir)) {
    echo "Signs base directory not found: $baseDir\n";
    exit(1);
}

// We will walk immediate subdirectories (letters, words, english, arabic) and also files at base
$dir = $baseDir;
if (!is_dir($dir)) {
    echo "Letters directory not found: $dir\n";
    exit(1);
}

$created = 0;
$updated = 0;
$skipped = 0;

// Recursively iterate signs directory
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
foreach ($iterator as $fileInfo) {
    if ($fileInfo->isDir()) continue;
    $path = $fileInfo->getPathname();
    $file = $fileInfo->getFilename();

    $base = pathinfo($file, PATHINFO_FILENAME);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ['png','jpg','jpeg','webp'])) continue;

    // Determine language and key heuristics
    $lang = 'ar';
    $key = $base;

    // If begins with en_ or en- or en., mark english
    if (preg_match('/^en[_\-.]/i', $base)) {
        $lang = 'en';
        $key = preg_replace('/^en[_\-.]/i', '', $base);
        // For english we prefer uppercase single-letter keys
        $key = strtoupper($key);
    } elseif (preg_match('/^ar[_\-.]/i', $base)) {
        $lang = 'ar';
        $key = preg_replace('/^ar[_\-.]/i', '', $base);
    } else {
        // detect if filename contains latin letters only -> english
        if (preg_match('/^[A-Za-z]$/', $base) || preg_match('/^[A-Za-z_\-]/', $base)) {
            $lang = 'en';
            $key = strtoupper($base);
        } else {
            // keep as arabic, attempt to decode percent-encoding
            $decoded = rawurldecode($base);
            if ($decoded !== $base) $key = $decoded;
            // normalize common Arabic letters
        }
    }

    // Build src relative to storage root (normalizeSrc expects path without leading slash)
    $rel = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $path);
    $rel = str_replace('\\', '/', $rel);
    $src = ltrim($rel, '/'); // e.g., storage/signs/words/مرحبا.png
    // ensure it's under signs/
    if (strpos($src, 'storage/signs/') === 0) {
        $src = substr($src, strlen('storage/'));
    }
    // category by containing folder name
    $category = 'letters';
    if (stripos($path, DIRECTORY_SEPARATOR . 'words' . DIRECTORY_SEPARATOR) !== false) {
        $category = 'words';
    } elseif (stripos($path, DIRECTORY_SEPARATOR . 'arabic' . DIRECTORY_SEPARATOR) !== false) {
        $category = 'arabic';
    } elseif (stripos($path, DIRECTORY_SEPARATOR . 'english' . DIRECTORY_SEPARATOR) !== false) {
        $category = 'english';
    }

    // Try find existing by src or key
    $existing = SignAsset::where('src', $src)->orWhere('key', $key)->first();
    if ($existing) {
        // ensure active and src set
        $changed = false;
        if ($existing->src !== $src) { $existing->src = $src; $changed = true; }
        if (!$existing->active) { $existing->active = true; $changed = true; }
        if ($changed) { $existing->save(); $updated++; } else { $skipped++; }
        continue;
    }

    // Create new record
    $text = $key;
    if ($lang === 'ar') {
        // if key is percent-encoded or hex, try to provide readable text
        $decoded = rawurldecode($key);
        if ($decoded !== $key) $text = $decoded;
    }

    SignAsset::create([
        'key' => $key,
        'language' => $lang,
        'type' => 'image',
        'src' => $src,
        'text' => $text,
        'active' => true,
        'category' => 'letters',
        'description' => 'Indexed from filesystem'
    ]);
    $created++;
}

echo "Indexed images: created={$created}, updated={$updated}, skipped={$skipped}\n";
