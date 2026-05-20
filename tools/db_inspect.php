<?php
require_once __DIR__ . '/../config.php';

function fetch_all_rows(mysqli_result $res): array {
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function list_tables(mysqli $conn): array {
    $res = $conn->query("SHOW TABLES");
    if (!$res) return [];
    $rows = fetch_all_rows($res);
    $tables = [];
    foreach ($rows as $row) {
        $tables[] = array_values($row)[0];
    }
    sort($tables);
    return $tables;
}

function describe_table(mysqli $conn, string $table): array {
    $stmt = $conn->prepare("DESCRIBE `$table`");
    if (!$stmt) return [];
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) return [];
    return fetch_all_rows($res);
}

function print_table_columns(mysqli $conn, string $table): void {
    echo "== $table ==" . PHP_EOL;
    $cols = describe_table($conn, $table);
    if (!$cols) {
        echo "(missing or no permission)" . PHP_EOL . PHP_EOL;
        return;
    }
    foreach ($cols as $c) {
        echo "- {$c['Field']} {$c['Type']}" . ($c['Null'] === 'NO' ? ' NOT NULL' : '') . (isset($c['Key']) && $c['Key'] ? " KEY={$c['Key']}" : '') . PHP_EOL;
    }
    echo PHP_EOL;
}

$tables = list_tables($conn);
echo "Database: " . DB_NAME . PHP_EOL;
echo "Tables (" . count($tables) . "): " . implode(', ', $tables) . PHP_EOL . PHP_EOL;

foreach (['customers', 'amazon_business_report', 'amazon_detail_report', 'financial_settings'] as $t) {
    print_table_columns($conn, $t);
}

