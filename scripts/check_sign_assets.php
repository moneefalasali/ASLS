<?php
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo "DB not found at $db\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT id, `key`, language, src, active FROM sign_assets WHERE language='en' LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "No english sign_assets found.\n";
    } else {
        foreach ($rows as $r) {
            echo "ID: {$r['id']}, key: {$r['key']}, lang: {$r['language']}, src: {$r['src']}, active: {$r['active']}\n";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
