<?php
// Move legacy icon files into a backup folder under public/frontend/legacy-icons
$base = __DIR__ . '/../public/frontend';
$backup = $base . '/legacy-icons';
if (!is_dir($backup)) mkdir($backup, 0755, true);

$keep = ['app-icon-192.png', 'app-icon-512.png'];
$patterns = ['logo.png', 'icon-*.png', 'apple-touch-icon.png', 'favicon*', 'badge-*.png', 'action-*.png', 'shortcut-*.png', 'logo-*.png'];

$filesMoved = 0;
foreach (glob($base . '/icon-*.png') as $f) {
    $name = basename($f);
    if (!in_array($name, $keep)) {
        rename($f, $backup . '/' . $name);
        echo "Moved $name to legacy-icons/\n";
        $filesMoved++;
    }
}
foreach ($patterns as $pat) {
    foreach (glob($base . '/' . $pat) as $f) {
        $name = basename($f);
        if (!in_array($name, $keep)) {
            rename($f, $backup . '/' . $name);
            echo "Moved $name to legacy-icons/\n";
            $filesMoved++;
        }
    }
}

// Move logo.png if present
if (file_exists($base . '/logo.png') && !in_array('logo.png', $keep)) {
    rename($base . '/logo.png', $backup . '/logo.png');
    echo "Moved logo.png to legacy-icons/\n";
    $filesMoved++;
}

if ($filesMoved === 0) {
    echo "No legacy icons found to move.\n";
} else {
    echo "Total moved: $filesMoved\n";
}
