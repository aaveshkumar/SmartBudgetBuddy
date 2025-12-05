<?php
require_once __DIR__ . '/../config/database.php';

echo "Creating database tables...\n\n";

$db = getDB();

$tables = [
    'notifications' => "
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient_id INT NOT NULL,
            actor_id INT NULL,
            related_type ENUM('job', 'application', 'system') NOT NULL DEFAULT 'system',
            related_id INT NULL,
            type ENUM('job_selected', 'new_job', 'system', 'chat_message') NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_recipient_read (recipient_id, is_read),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    'conversations' => "
        CREATE TABLE IF NOT EXISTS conversations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employer_id INT NOT NULL,
            candidate_id INT NOT NULL,
            job_id INT NOT NULL,
            application_id INT NOT NULL,
            status ENUM('active', 'closed') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_conversation (employer_id, candidate_id, job_id),
            INDEX idx_employer (employer_id),
            INDEX idx_candidate (candidate_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    'conversation_messages' => "
        CREATE TABLE IF NOT EXISTS conversation_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT NOT NULL,
            sender_id INT NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_conversation (conversation_id),
            INDEX idx_sender (sender_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    'reports' => "
        CREATE TABLE IF NOT EXISTS reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reporter_id INT NOT NULL,
            reported_type ENUM('job', 'user') NOT NULL,
            reported_id INT NOT NULL,
            message TEXT NOT NULL,
            status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_reported (reported_type, reported_id),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "
];

foreach ($tables as $name => $sql) {
    echo "Creating table: $name\n";
    try {
        $db->exec($sql);
        echo "  ✓ $name created successfully\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "  ✓ $name already exists\n";
        } else {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nVerifying tables exist:\n";
foreach (array_keys($tables) as $table) {
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

echo "\nDone!\n";
