<?php
/**
 * Add Comprehensive Job Seeker Profile Tables
 * Run this to enable advanced job seeker profile features
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    echo "Creating job seeker profile tables...\n\n";
    
    // 1. Main jobseeker profiles table
    echo "1. Creating jobseeker_profiles table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS jobseeker_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        headline VARCHAR(200),
        summary TEXT,
        phone VARCHAR(20),
        address TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        country VARCHAR(100) DEFAULT 'India',
        postal_code VARCHAR(20),
        date_of_birth DATE,
        gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
        profile_picture VARCHAR(255),
        resume_file VARCHAR(255),
        linkedin_url VARCHAR(255),
        github_url VARCHAR(255),
        portfolio_url VARCHAR(255),
        total_experience_years INT DEFAULT 0,
        current_salary DECIMAL(10,2),
        expected_salary DECIMAL(10,2),
        notice_period VARCHAR(50),
        availability ENUM('immediate', '15_days', '1_month', '2_months', '3_months') DEFAULT 'immediate',
        willing_to_relocate BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_city (city),
        INDEX idx_experience (total_experience_years),
        INDEX idx_availability (availability)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ jobseeker_profiles table created\n";
    
    // 2. Work experience table
    echo "2. Creating work_experiences table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS work_experiences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        job_title VARCHAR(200) NOT NULL,
        company_name VARCHAR(200) NOT NULL,
        location VARCHAR(200),
        employment_type ENUM('full_time', 'part_time', 'contract', 'internship', 'freelance') DEFAULT 'full_time',
        start_date DATE NOT NULL,
        end_date DATE,
        is_current BOOLEAN DEFAULT FALSE,
        description TEXT,
        achievements TEXT,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ work_experiences table created\n";
    
    // 3. Education table
    echo "3. Creating education table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS education (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        degree VARCHAR(200) NOT NULL,
        field_of_study VARCHAR(200),
        institution VARCHAR(200) NOT NULL,
        location VARCHAR(200),
        start_date DATE,
        end_date DATE,
        grade VARCHAR(50),
        description TEXT,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ education table created\n";
    
    // 4. Skills table
    echo "4. Creating skills table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        proficiency ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
        years_of_experience INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id),
        INDEX idx_skill_name (skill_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ skills table created\n";
    
    // 5. Certifications table
    echo "5. Creating certifications table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS certifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        certification_name VARCHAR(200) NOT NULL,
        issuing_organization VARCHAR(200) NOT NULL,
        issue_date DATE,
        expiry_date DATE,
        credential_id VARCHAR(200),
        credential_url VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ certifications table created\n";
    
    // 6. Languages table
    echo "6. Creating languages table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS languages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language_name VARCHAR(100) NOT NULL,
        proficiency ENUM('basic', 'conversational', 'professional', 'native') DEFAULT 'professional',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ languages table created\n";
    
    // 7. Projects table
    echo "7. Creating projects table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        project_name VARCHAR(200) NOT NULL,
        project_url VARCHAR(255),
        description TEXT,
        technologies_used TEXT,
        start_date DATE,
        end_date DATE,
        is_ongoing BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $db->exec($sql);
    echo "   ✓ projects table created\n";
    
    echo "\n✅ All job seeker profile tables created successfully!\n";
    echo "\nJob seekers can now create comprehensive profiles with:\n";
    echo "  • Personal information & contact details\n";
    echo "  • Work experience history\n";
    echo "  • Educational background\n";
    echo "  • Skills with proficiency levels\n";
    echo "  • Certifications & credentials\n";
    echo "  • Language proficiencies\n";
    echo "  • Projects & portfolio\n";
    echo "  • Resume file upload (PDF/DOCX)\n";
    echo "  • Profile picture upload\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
