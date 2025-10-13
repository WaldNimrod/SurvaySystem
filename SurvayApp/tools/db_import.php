<?php
// Simple DB import tool using mysqli. Reads fresh.sql and executes statements.

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('MEZOO_DB_HOST') ?: '127.0.0.1';
$port = (int)(getenv('MEZOO_DB_PORT') ?: 3306);
$user = getenv('MEZOO_DB_USER') ?: 'root';
$pass = getenv('MEZOO_DB_PASS') ?: 'root';
$dbname = getenv('MEZOO_DB_NAME') ?: 'mezoo';

$root = dirname(__DIR__);
$freshSqlPath = $root . '/till.mezoo.co.il_bm1756763301dm/fresh.sql';
if (!is_file($freshSqlPath)) {
    fwrite(STDERR, "fresh.sql not found at: $freshSqlPath\n");
    exit(1);
}

$mysqli = @new mysqli($host, $user, $pass, '', $port);
if ($mysqli->connect_error) {
    fwrite(STDERR, "MySQL connection failed: {$mysqli->connect_error}\n");
    exit(2);
}

// Create DB if needed
$mysqli->query("CREATE DATABASE IF NOT EXISTS `".$mysqli->real_escape_string($dbname)."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
if ($mysqli->errno) {
    fwrite(STDERR, "Create DB error: {$mysqli->error}\n");
    exit(3);
}

if (!$mysqli->select_db($dbname)) {
    fwrite(STDERR, "Select DB error: {$mysqli->error}\n");
    exit(4);
}

$sql = file_get_contents($freshSqlPath);
// Normalize line endings
$sql = str_replace(["\r\n", "\r"], "\n", $sql);
$statements = array_filter(array_map('trim', explode(';', $sql)));

$ok = 0; $fail = 0;
foreach ($statements as $stmt) {
    if ($stmt === '' || strpos($stmt, '/*') === 0 || strpos($stmt, '--') === 0) {
        continue;
    }
    if (!$mysqli->query($stmt)) {
        $fail++;
        fwrite(STDERR, "Error: {$mysqli->error} for statement: \n$stmt;\n\n");
    } else {
        $ok++;
    }
}

echo "Imported SQL statements. OK=$ok FAIL=$fail\n";
exit($fail ? 5 : 0);


