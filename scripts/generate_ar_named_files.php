<?php
$root = __DIR__ . '/../public/storage/signs';
$arabicDir = $root . '/arabic';
$lettersDir = $root . '/letters';
if (!is_dir($lettersDir)) mkdir($lettersDir, 0755, true);

// Map Arabic char -> descriptive filename suffix (without .png)
$map = [
    'ا' => 'alif',
    'أ' => 'alif',
    'ب' => 'baa',
    'ت' => 'taa',
    'ث' => 'thaa',
    'ج' => 'jeem',
    'ح' => 'haa',
    'خ' => 'khaa',
    'د' => 'daal',
    'ذ' => 'thaal',
    'ر' => 'raa',
    'ز' => 'zaay',
    'س' => 'seen',
    'ش' => 'sheen',
    'ص' => 'saad',
    'ض' => 'daad',
    'ط' => 'taa_h',
    'ظ' => 'zaa',
    'ع' => 'ayn',
    'غ' => 'ghayn',
    'ف' => 'faa',
    'ق' => 'qaaf',
    'ك' => 'kaaf',
    'ل' => 'laam',
    'م' => 'meem',
    'ن' => 'noon',
    'ه' => 'haa_w',
    'و' => 'waaw',
    'ي' => 'yaa'
];

$copied = 0;
foreach ($map as $char => $name) {
    $srcCandidates = [];
    // prefer existing named file in letters (decoded)
    $candidate1 = $lettersDir . '/ar_' . $char . '.png';
    if (is_file($candidate1)) $srcCandidates[] = $candidate1;
    // fallback to percent-encoded file
    $enc = rawurlencode($char);
    $candidate2 = $lettersDir . '/ar_' . $enc . '.png';
    if (is_file($candidate2)) $srcCandidates[] = $candidate2;
    // fallback to arabic/<char>.png
    $candidate3 = $arabicDir . '/' . $char . '.png';
    if (is_file($candidate3)) $srcCandidates[] = $candidate3;

    if (empty($srcCandidates)) {
        echo "No source image found for $char -> $name (tried candidates)\n";
        continue;
    }

    $src = $srcCandidates[0];
    $dst = $lettersDir . '/ar_' . $name . '.png';
    if (!file_exists($dst)) {
        if (copy($src, $dst)) {
            echo "Created $dst from " . basename($src) . "\n";
            $copied++;
        } else {
            echo "Failed to copy for $char -> $name\n";
        }
    } else {
        echo "Already exists: " . basename($dst) . "\n";
    }
}

echo "Done. Created named files: $copied\n";
