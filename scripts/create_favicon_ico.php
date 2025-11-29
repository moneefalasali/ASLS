<?php
// Creates a favicon.ico in public/ from a PNG source
// This writes a single-entry ICO containing a 32x32 PNG image.
// Usage: php scripts/create_favicon_ico.php [source-png]

$src = $argv[1] ?? __DIR__ . '/../public/frontend/app-icon-192.png';
if (!file_exists($src)) {
    echo "Source PNG not found: $src\n";
    exit(1);
}

// Create a 32x32 PNG image (tmp) using GD
$img = imagecreatefromstring(file_get_contents($src));
if (!$img) { echo "Failed to read source image.\n"; exit(1); }
$w = imagesx($img); $h = imagesy($img);
$size = 32;
$tmp = sys_get_temp_dir() . '/favicon_' . time() . '.png';
$out = imagecreatetruecolor($size, $size);
imagesavealpha($out, true);
$trans = imagecolorallocatealpha($out, 0,0,0,127);
imagefill($out, 0,0, $trans);
// compute resized area
$ratio = min($size/$w, $size/$h);
$nw = (int)round($w * $ratio);
$nh = (int)round($h * $ratio);
$dstX = (int)(($size - $nw)/2);
$dstY = (int)(($size - $nh)/2);
imagecopyresampled($out, $img, $dstX, $dstY, 0,0, $nw, $nh, $w, $h);
imagepng($out, $tmp);
imagedestroy($out);
imagedestroy($img);

$pngData = file_get_contents($tmp);
unlink($tmp);

// Build ICO file header and single PNG image entry
$iconDir = pack('vvv', 0, 1, 1); // reserved, type=1 (icon), count=1

$width = 32; $height = 32; $colorCount = 0; $reserved = 0; $planes = 0; $bitCount = 32;
$bytesInRes = strlen($pngData);
$imageOffset = 6 + 16 * 1; // header size + entry size

$entry = pack('C', $width) . pack('C', $height) . pack('C', $colorCount) . pack('C', $reserved)
    . pack('v', $planes) . pack('v', $bitCount) . pack('V', $bytesInRes) . pack('V', $imageOffset);

$icoData = $iconDir . $entry . $pngData;

$dest = __DIR__ . '/../public/favicon.ico';
file_put_contents($dest, $icoData);
@chmod($dest, 0644);

echo "Created favicon: $dest (" . filesize($dest) . " bytes)\n";
exit(0);
