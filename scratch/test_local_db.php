<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=keuangan_bkm', 'root', '');
    echo "SUCCESS localhost connection!\n";
} catch (\Throwable $e) {
    echo "localhost Error: " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=keuangan_bkm', 'root', '');
    echo "SUCCESS 127.0.0.1:3306 connection!\n";
} catch (\Throwable $e) {
    echo "127.0.0.1 Error: " . $e->getMessage() . "\n";
}
