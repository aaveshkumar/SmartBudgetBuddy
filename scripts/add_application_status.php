<?php
/**
 * Add status column to applications table
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    echo "Adding status column to applications table...\n";
    
    $sql = "ALTER TABLE applications ADD COLUMN status VARCHAR(50) DEFAULT 'pending'";
    
    try {
        $db->exec($sql);
        echo "✓ Status column added successfully!\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Status column already exists.\n";
        } else {
            throw $e;
        }
    }
    
    echo "\n✓ Migration completed!\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
