<?php

$hosts = [
    'auth-db1409.hstgr.io',
    'srv1409.hstgr.io',
    'logistik.ptberkahkaryamansyur.com',
];

$userKeuangan = 'u957149181_keuangan';
$passKeuangan = 'k4uANGAN000';
$dbKeuangan   = 'u957149181_keuangan';

$userLogistik = 'u957149181_logistik_bkm';
$passLogistik = 'Logistikbkm123';
$dbLogistik   = 'u957149181_logistik_bkm';

foreach ($hosts as $host) {
    echo "=== Testing Host: {$host} ===\n";
    try {
        $pdo = new PDO("mysql:host={$host};port=3306;dbname={$dbKeuangan}", $userKeuangan, $passKeuangan, [PDO::ATTR_TIMEOUT => 5]);
        echo "SUCCESS connecting Keuangan DB on {$host}!\n";
    } catch (\Throwable $e) {
        echo "Keuangan DB Error on {$host}: " . $e->getMessage() . "\n";
    }

    try {
        $pdo = new PDO("mysql:host={$host};port=3306;dbname={$dbLogistik}", $userLogistik, $passLogistik, [PDO::ATTR_TIMEOUT => 5]);
        echo "SUCCESS connecting Logistik DB on {$host}!\n";
    } catch (\Throwable $e) {
        echo "Logistik DB Error on {$host}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
