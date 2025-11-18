<?php
$base = __DIR__ . '/../public/storage/signs';
$srcPattern = $base . '/en_*.png';
$dstDir = $base . '/letters';
if (!is_dir($dstDir)) {
    mkdir($dstDir, 0755, true);
    echo "Created $dstDir\n";
} else {
    echo "Directory exists: $dstDir\n";
}
$files = glob($srcPattern);
foreach ($files as $f) {
    $name = basename($f);
    $dest = $dstDir . '/' . $name;
    if (!copy($f, $dest)) {
        echo "Failed to copy $f -> $dest\n";
    } else {
        echo "Copied $name -> letters/$name\n";
    }
}
// Also copy arabic files if any exist under english or arabic folders
$arabicSrc = $base . '/arabic/*.png';
foreach (glob($arabicSrc) as $f) {
    $name = basename($f);
    $dest = $dstDir . '/' . $name;
    if (!copy($f, $dest)) {
        echo "Failed to copy $f -> $dest\n";
    } else {
        echo "Copied arabic $name -> letters/$name\n";
    }
}
