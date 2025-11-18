<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$counts = [];
foreach (['ar','en'] as $lang) {
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM sign_assets WHERE language='$lang'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $counts[$lang] = $row['c'] ?? 0;
}
print_r($counts);
