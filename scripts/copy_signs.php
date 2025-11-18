<?php
$srcDir = __DIR__ . '/../public/storage/signs/english';
$destDir = __DIR__ . '/../public/storage/signs';
if (!is_dir($srcDir)) {
    echo "Source directory not found: $srcDir\n";
    exit(1);
}
$files = glob($srcDir . '/*.png');
foreach ($files as $f) {
    $name = pathinfo($f, PATHINFO_FILENAME);
    $dest = $destDir . '/en_' . $name . '.png';
    if (!copy($f, $dest)) {
        echo "Failed to copy $f -> $dest\n";
    } else {
        echo "Copied $f -> $dest\n";
    }
}
