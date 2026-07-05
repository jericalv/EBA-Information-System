<?php
// Quick DB export script - run with: php database/export_db.php
$host = '127.0.0.1';
$db   = 'eba_capstone';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$output = [];
$output[] = "-- EBA Capstone DB Export -- " . date('Y-m-d H:i:s');
$output[] = "SET FOREIGN_KEY_CHECKS=0;";
$output[] = "";

foreach ($tables as $table) {
    // Get CREATE TABLE
    $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $output[] = "DROP TABLE IF EXISTS `$table`;";
    $output[] = $create['Create Table'] . ";";
    $output[] = "";

    // Get data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $cols = implode('`, `', array_keys($row));
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), array_values($row));
        $output[] = "INSERT INTO `$table` (`$cols`) VALUES (" . implode(', ', $vals) . ");";
    }
    $output[] = "";
}

$output[] = "SET FOREIGN_KEY_CHECKS=1;";

file_put_contents(__DIR__ . '/eba_capstone.sql', implode("\n", $output));
echo "Done. Tables exported: " . count($tables) . "\n";
foreach ($tables as $t) echo "  - $t\n";
