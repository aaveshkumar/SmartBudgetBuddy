<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    $sql = "ALTER TABLE conversation_messages 
            ADD COLUMN IF NOT EXISTS attachment_path VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS attachment_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS attachment_type VARCHAR(100) DEFAULT NULL";
    
    $db->exec($sql);
    
    echo "Successfully added attachment columns to conversation_messages table.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
