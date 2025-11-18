<?php
$src = 'c:\\xampp\\htdocs\\a_i_s_l_project\\الكلمات الاساسية في لغة الاشارة';
$dst = 'c:\\xampp\\htdocs\\a_i_s_l_project\\si\\public\\storage\\signs\\words';
if (!is_dir($src)) {
    echo "Source not found: $src\n";
    exit(1);
}
if (!is_dir($dst)) {
    if (!mkdir($dst, 0755, true)) { echo "Failed to create dest $dst\n"; exit(1);} 
}
$files = glob($src . DIRECTORY_SEPARATOR . "*.png");
$copied = 0;
foreach ($files as $f) {
    $name = basename($f);
    $norm = str_replace(' ', '_', $name);
    $target = $dst . DIRECTORY_SEPARATOR . $norm;
    if (!file_exists($target)) {
        if (copy($f, $target)) { echo "Copied: $norm\n"; $copied++; }
        else echo "Copy failed: $norm\n";
    } else {
        echo "Exists: $norm\n";
    }
}
echo "Total copied: $copied\n";
