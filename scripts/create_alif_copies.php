<?php
$lettersDir = __DIR__ . '/../public/storage/signs/letters';
$sourceCandidates = [
    $lettersDir . '/ar_أ.png',
    $lettersDir . '/ar_%D8%A3.png',
    __DIR__ . '/../public/storage/signs/arabic/أ.png'
];
$found = null;
foreach ($sourceCandidates as $s) {
    if (file_exists($s)) { $found = $s; break; }
}
if (!$found) {
    echo "Source alif image not found.\n";
    exit(1);
}
$targets = [
    $lettersDir . '/ar_%D8%A7.png',
    $lettersDir . '/ar_ا.png',
    $lettersDir . '/ar_alif.png'
];
$created = 0;
foreach ($targets as $t) {
    if (!file_exists($t)) {
        if (copy($found, $t)) { echo "Created " . basename($t) . " from " . basename($found) . "\n"; $created++; }
        else echo "Failed to create " . basename($t) . "\n";
    } else {
        echo "Already exists: " . basename($t) . "\n";
    }
}
echo "Done. Created: $created\n";
