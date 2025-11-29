<?php
/**
 * Database Setup Script
 * Run this to create tables and insert sample data
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    echo "Reading SQL file...\n";
    $sql = file_get_contents(__DIR__ . '/../docs/sample_data.sql');
    
    if ($sql === false) {
        die("Error: Could not read SQL file\n");
    }
    
    echo "Executing SQL statements...\n";
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // Split SQL file by semicolons and execute each statement
    $statements = explode(';', $sql);
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip empty statements
        if (empty($statement)) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            // Show what was executed
            $firstLine = strtok($statement, "\n");
            echo "✓ " . substr($firstLine, 0, 60) . "...\n";
            
        } catch (PDOException $e) {
            echo "Warning: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    
    echo "\n✓ Database setup completed!\n";
    echo "Statements executed successfully: $successCount\n";
    if ($errorCount > 0) {
        echo "Statements with warnings: $errorCount\n";
    }
    echo "\nDefault login credentials:\n";
    echo "Admin: admin@connectwith9.com / password123\n";
    echo "Employer: employer1@example.com / password123\n";
    echo "Job Seeker: jobseeker1@example.com / password123\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
