<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=logistik_bkm;port=3306', 'root', '');
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables in local logistik_bkm:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo " - " . $row[0] . "\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
