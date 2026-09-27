<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=logistik_bkm;port=3306', 'root', '');
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
    $sql = file_get_contents(__DIR__ . '/../kebutuhan_langsung.sql');
    $pdo->exec($sql);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    echo "kebutuhan_langsung.sql imported successfully into local logistik_bkm!\n";
} catch (\Throwable $e) {
    echo "Import Error: " . $e->getMessage() . "\n";
}
