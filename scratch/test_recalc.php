<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$t0 = microtime(true);
DB::statement('SET @s := 0;');
DB::statement('UPDATE kas_raab SET saldo = (@s := @s + COALESCE(kredit, 0) - COALESCE(debet, 0)) ORDER BY id ASC;');
$t1 = microtime(true);

echo 'Kas RAAB recalculate completed in ' . round(($t1 - $t0) * 1000, 2) . ' ms' . PHP_EOL;

$t2 = microtime(true);
DB::statement('SET @s := 0;');
DB::statement('UPDATE kas_bkm SET saldo_kas = (@s := @s + COALESCE(debet, 0) - COALESCE(kredit, 0)) ORDER BY id ASC;');
$t3 = microtime(true);

echo 'Kas BKM recalculate completed in ' . round(($t3 - $t2) * 1000, 2) . ' ms' . PHP_EOL;
