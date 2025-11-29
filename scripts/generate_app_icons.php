<?php
// Generate app icons from a source logo image
// Usage: php scripts/generate_app_icons.php [source_image]

$srcArg = $argv[1] ?? null;
$sourceCandidates = [
    __DIR__ . '/../public/frontend/logo.png',
    __DIR__ . '/../public/frontend/app-icon-src.png',
];
if ($srcArg) {
    $sourceCandidates = [realpath($srcArg) ?: $srcArg];
}

$source = null;
foreach ($sourceCandidates as $c) {
    if ($c && file_exists($c)) { $source = $c; break; }
}

if (!$source) {
    echo "Source logo not found. Place your logo at public/frontend/logo.png or provide path as argument.\n";
    exit(1);
}

$outDir = __DIR__ . '/../public/frontend';
$out192 = $outDir . '/app-icon-192.png';
$out512 = $outDir . '/app-icon-512.png';
$favicon = __DIR__ . '/../public/favicon.ico';

function resizeWithImagick($src, $dst, $size)
{
    $im = new \Imagick($src);
    $im->setImageBackgroundColor(new \ImagickPixel('transparent'));
    $im->thumbnailImage($size, $size, true, true);
    $im->setImageFormat('png');
    $im->writeImage($dst);
    $im->clear(); $im->destroy();
}

function resizeWithGD($src, $dst, $size)
{
    $data = file_get_contents($src);
    $img = imagecreatefromstring($data);
    if (!$img) return false;
    $w = imagesx($img); $h = imagesy($img);
    $ratio = min($size / $w, $size / $h);
    $nw = (int)round($w * $ratio); $nh = (int)round($h * $ratio);
    $out = imagecreatetruecolor($size, $size);
    imagesavealpha($out, true);
    $trans_colour = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefill($out, 0, 0, $trans_colour);
    $dstX = (int)(($size - $nw) / 2); $dstY = (int)(($size - $nh) / 2);
    imagecopyresampled($out, $img, $dstX, $dstY, 0, 0, $nw, $nh, $w, $h);
    imagepng($out, $dst);
    imagedestroy($out); imagedestroy($img);
    return true;
}

echo "Source image: $source\n";

// backup existing icons
foreach ([$out192, $out512, $favicon] as $f) {
    if (file_exists($f)) {
        $bak = $f . '.' . time() . '.bak';
        rename($f, $bak);
        echo "Backed up $f -> $bak\n";
    }
}

if (extension_loaded('imagick')) {
    try {
        resizeWithImagick($source, $out192, 192);
        resizeWithImagick($source, $out512, 512);
        // Generate ICO with 16,32,48 sizes if Imagick supports it
        $ico = new \Imagick();
        $sizes = [16,32,48,64,128,256];
        foreach ($sizes as $s) {
            $tmp = sys_get_temp_dir() . '/app_icon_' . $s . '.png';
            resizeWithImagick($source, $tmp, $s);
            $ico->readImage($tmp);
            unlink($tmp);
        }
        $ico->setImageFormat('ico');
        $ico->writeImage($favicon);
        $ico->clear(); $ico->destroy();
        echo "Generated icons using Imagick: $out192, $out512 and favicon.ico.\n";
    } catch (\Throwable $e) {
        echo "Imagick failed: " . $e->getMessage() . "\n";
        // fallback to GD
    }
}

if (!file_exists($out192) || !file_exists($out512)) {
    // Use GD fallback
    if (!extension_loaded('gd')) {
        echo "GD extension is not available. Install Imagick or enable GD to generate icons.\n";
        exit(1);
    }
    if (!resizeWithGD($source, $out192, 192)) { echo "Failed creating 192px icon.\n"; }
    if (!resizeWithGD($source, $out512, 512)) { echo "Failed creating 512px icon.\n"; }
    // Try to create a favicon (32px) as simple PNG (not ICO). Many browsers accept PNG as favicon.
    $tmpFavicon = __DIR__ . '/../public/frontend/favicon.png';
    if (resizeWithGD($source, $tmpFavicon, 64)) {
        // If possible, also create .ico using ImageMagick if available
        if (extension_loaded('imagick')) {
            $ico = new \Imagick();
            $sizes = [16,32,48,64];
            foreach ($sizes as $s) {
                $tmp = sys_get_temp_dir() . '/app_icon_' . $s . '.png';
                resizeWithGD($source, $tmp, $s);
                $ico->readImage($tmp);
                unlink($tmp);
            }
            $ico->setImageFormat('ico');
            $ico->writeImage($favicon);
            $ico->clear(); $ico->destroy();
            unlink($tmpFavicon);
        }
    }
    echo "Generated icons using GD: $out192, $out512 (and $favicon if available).\n";
}

// Set permissions
@chmod($out192, 0644);
@chmod($out512, 0644);
if (file_exists($favicon)) @chmod($favicon, 0644);

echo "Done.\n";
