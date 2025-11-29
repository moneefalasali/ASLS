<?php
$path = __DIR__ . '/../public/frontend/app-icon-192.png';
if (!file_exists($path)) { echo "ERROR: file not found: $path\n"; exit(2); }
$img = @imagecreatefromstring(file_get_contents($path));
if (!$img) { echo "ERROR: failed to load image (GD support required)\n"; exit(3); }
$w = imagesx($img); $h = imagesy($img);
$counts = [];
$step = max(1, intval(min($w,$h)/80));
for ($x=0;$x<$w;$x+=$step) {
  for ($y=0;$y<$h;$y+=$step) {
    $rgb = imagecolorat($img,$x,$y);
    $r = ($rgb>>16)&0xFF; $g = ($rgb>>8)&0xFF; $b = $rgb&0xFF;
    // ignore near-white/transparent pixels
    if ($r>240 && $g>240 && $b>240) continue;
    // quantize to 8-bit -> 5-bit blocks to reduce variety
    $qr = intval($r/8); $qg = intval($g/8); $qb = intval($b/8);
    $key = sprintf('%02X%02X%02X', $qr, $qg, $qb);
    if (!isset($counts[$key])) $counts[$key]=0;
    $counts[$key]++;
  }
}
if (empty($counts)) { echo "fallback_hex=#F8F4EE\n"; exit(0); }
arsort($counts);
$topKey = array_key_first($counts);
// expand quantized back to approximate full color
$qr = hexdec(substr($topKey,0,2)); $qg = hexdec(substr($topKey,2,2)); $qb = hexdec(substr($topKey,4,2));
$approxR = min(255, $qr*8+4); $approxG = min(255, $qg*8+4); $approxB = min(255, $qb*8+4);
$hex = sprintf('#%02X%02X%02X', $approxR, $approxG, $approxB);
echo "prominent_hex={$hex}\n";
exit(0);
