<?php
// Simple average color extractor for a PNG/JPG logo
$path = __DIR__ . '/../public/frontend/app-icon-192.png';
if (!file_exists($path)) {
    echo "ERROR: file not found: $path\n";
    exit(2);
}
$img = @imagecreatefromstring(file_get_contents($path));
if (!$img) {
    echo "ERROR: failed to load image (GD support required)\n";
    exit(3);
}
$w = imagesx($img);
$h = imagesy($img);
$totalR = $totalG = $totalB = $count = 0;
$step = max(1, intval(min($w, $h) / 50)); // sample up to ~2500 pixels
for ($x = 0; $x < $w; $x += $step) {
    for ($y = 0; $y < $h; $y += $step) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $totalR += $r; $totalG += $g; $totalB += $b; $count++;
    }
}
if ($count === 0) { echo "ERROR: no pixels sampled\n"; exit(4); }
$avgR = intval($totalR / $count);
$avgG = intval($totalG / $count);
$avgB = intval($totalB / $count);
$hex = sprintf('#%02X%02X%02X', $avgR, $avgG, $avgB);
echo "dominant_hex={$hex}\n";
exit(0);
