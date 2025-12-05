<?php
require_once __DIR__ . '/../config/database.php';

echo "Running database migrations...\n\n";

$db = getDB();

$migrations = [
    '003_notifications_chat.sql',
    '004_reports.sql'
];

foreach ($migrations as $migration) {
    $file = __DIR__ . '/../database/migrations/' . $migration;
    if (!file_exists($file)) {
        echo "Migration file not found: $migration\n";
        continue;
    }
    
    echo "Running migration: $migration\n";
    $sql = file_get_contents($file);
    
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $db->exec($statement);
            echo "  - Statement executed successfully\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "  - Table already exists, skipping\n";
            } else {
                echo "  - Error: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n";
}

echo "Migrations complete!\n";

echo "\nVerifying tables exist:\n";
$tables = ['notifications', 'conversations', 'conversation_messages', 'reports'];
foreach ($tables as $table) {
    try {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "  ✓ $table exists\n";
        } else {
            echo "  ✗ $table does NOT exist\n";
        }
    } catch (PDOException $e) {
        echo "  ✗ Error checking $table: " . $e->getMessage() . "\n";
    }
}
