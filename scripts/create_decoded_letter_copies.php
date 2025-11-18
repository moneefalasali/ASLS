<?php
$lettersDir = __DIR__ . '/../public/storage/signs/letters';
if (!is_dir($lettersDir)) {
    echo "letters dir not found: $lettersDir\n";
    exit(1);
}
$files = scandir($lettersDir);
$created = 0;
foreach ($files as $f) {
    if (!preg_match('/^ar_(%[0-9A-Fa-f]{2})+\.png$/', $f)) continue;
    $full = $lettersDir . '/' . $f;
    if (!is_file($full)) continue;
    // extract encoded part (between ar_ and .png)
    $encoded = substr($f, 3, -4); // remove ar_ and .png
    $decoded = rawurldecode($encoded);
    // sanitized decoded filename
    $dst = $lettersDir . '/ar_' . $decoded . '.png';
    if (!file_exists($dst)) {
        if (copy($full, $dst)) {
            echo "Created decoded copy: " . basename($dst) . "\n";
            $created++;
        } else {
            echo "Failed to create decoded copy for $f\n";
        }
    } else {
        echo "Decoded copy already exists: " . basename($dst) . "\n";
    }
}
echo "Done. Created: $created\n";
