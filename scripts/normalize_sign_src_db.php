<?php
// Normalize sign_assets.src to 'signs/letters/...' (no leading /storage)
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo "Database file not found at: $db\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $rows = $pdo->query("SELECT id, src, language FROM sign_assets WHERE src IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0;
    foreach ($rows as $r) {
        $id = $r['id'];
        $src = trim($r['src']);
        if ($src === '') continue;

        // Remove leading / if present
        $s = ltrim($src, '/');
        // Remove leading storage/ if present
        if (str_starts_with($s, 'storage/')) {
            $s = substr($s, strlen('storage/'));
        }

        // If path already contains 'signs/letters', use that; else try to map en_* or ar_* or english/ etc.
        if (preg_match('#(^|/)letters/#', $s)) {
            $new = $s; // keep as-is (e.g., signs/letters/en_a.png)
        } else {
            // Try to locate filename
            $filename = basename($s);
            if (preg_match('/^en_[a-z]\\.png$/i', $filename) || preg_match('/^[a-z]\\.png$/i', $filename)) {
                $name = strtolower($filename);
                // if single letter like a.png -> en_a.png
                if (preg_match('/^[a-z]\\.png$/i', $name)) {
                    $name = 'en_' . pathinfo($name, PATHINFO_FILENAME) . '.png';
                }
                $new = 'signs/letters/' . $name;
            } elseif (preg_match('/^ar_.*\\.png$/i', $filename) || preg_match('/^ar_.*$/i', $s)) {
                $new = 'signs/letters/' . $filename;
            } else {
                // Fallback: keep under signs/ (preserve filename)
                $new = 'signs/' . $filename;
            }
        }

        if ($new !== $s) {
            $stmt = $pdo->prepare('UPDATE sign_assets SET src = :src, updated_at = :u WHERE id = :id');
            $stmt->execute([':src' => $new, ':u' => date('c'), ':id' => $id]);
            echo "Updated id=$id: $src -> $new\n";
            $updated++;
        }
    }
    echo "Done. Total updated: $updated\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
