<?php
/**
 * Add Password Reset Tokens Table
 * Run this to enable password reset functionality
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    echo "Creating password_reset_tokens table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(100) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_token (token),
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "✓ Table created successfully!\n";
    echo "\nPassword reset functionality is now enabled.\n";
    echo "Users can request password resets at: /forgot-password\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
