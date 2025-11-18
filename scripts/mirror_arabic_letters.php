<?php
$root = __DIR__ . '/../public/storage/signs';
$srcDir = $root . '/arabic';
$dstDir = $root . '/letters';
if (!is_dir($srcDir)) {
    echo "Source dir not found: $srcDir\n";
    exit(1);
}
if (!is_dir($dstDir)) {
    if (!mkdir($dstDir, 0755, true)) {
        echo "Failed to create dest dir: $dstDir\n";
        exit(1);
    }
}
$files = scandir($srcDir);
$copied = 0;
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $full = $srcDir . '/' . $f;
    if (!is_file($full)) continue;
    // compute rawurlencoded name
    $encoded = rawurlencode($f);
    $dstFile = $dstDir . '/ar_' . $encoded;
    if (!file_exists($dstFile)) {
        if (copy($full, $dstFile)) {
            echo "Copied $f -> letters/ar_$encoded\n";
            $copied++;
        } else {
            echo "Failed to copy $f\n";
        }
    } else {
        echo "Already exists: letters/ar_$encoded\n";
    }
}

echo "Done. Copied: $copied\n";
