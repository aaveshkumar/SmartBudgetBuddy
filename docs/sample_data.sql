-- ConnectWith9 Job Portal Database Schema

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type ENUM('jobseeker','employer','admin') DEFAULT 'jobseeker',
    verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Technologies Table
CREATE TABLE IF NOT EXISTS technologies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Education Levels Table
CREATE TABLE IF NOT EXISTS education_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs Table
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    employer_id INT NOT NULL,
    location VARCHAR(100) NOT NULL,
    salary VARCHAR(50),
    type ENUM('Full-time','Part-time','Contract','Internship') DEFAULT 'Full-time',
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_location (location),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applications Table
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, user_id),
    INDEX idx_job (job_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved Jobs Table
CREATE TABLE IF NOT EXISTS saved_jobs (
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(user_id, job_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Table (SEO Content)
CREATE TABLE IF NOT EXISTS blog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    meta_title VARCHAR(255),
    meta_description VARCHAR(160),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Logs Table
CREATE TABLE IF NOT EXISTS seo_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    meta_score INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login Attempts Table (for throttling)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data: Categories
INSERT INTO categories (name, slug) VALUES
('IT & Software', 'it-software'),
('Banking & Finance', 'banking-finance'),
('Marketing & Sales', 'marketing-sales'),
('Education & Training', 'education-training'),
('Healthcare & Medical', 'healthcare-medical'),
('Design & Creative', 'design-creative')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sample Data: Technologies
INSERT INTO technologies (name) VALUES
('PHP'), ('React'), ('Python'), ('Java'), ('Node.js'), ('MySQL'), 
('JavaScript'), ('HTML/CSS'), ('Angular'), ('Vue.js')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sample Data: Education Levels
INSERT INTO education_levels (name) VALUES
('10th Pass'), ('12th Pass'), ('Graduate'), ('Postgraduate'), ('Diploma'), ('Any')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sample Data: Users (passwords are 'password123')
INSERT INTO users (name, email, password, type, verified) VALUES
('Admin User', 'admin@connectwith9.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
('Tech Solutions Inc', 'employer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', 1),
('Marketing Pro Ltd', 'employer2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', 1),
('John Doe', 'jobseeker1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 1),
('Jane Smith', 'jobseeker2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jobseeker', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sample Data: Jobs
INSERT INTO jobs (title, description, category_id, employer_id, location, salary, type, status) VALUES
('Senior PHP Developer', 'We are looking for an experienced PHP developer to join our team. Must have 3+ years experience with PHP, MySQL, and modern frameworks.', 1, 2, 'Delhi', '50000-80000', 'Full-time', 'Approved'),
('React Frontend Developer', 'Seeking a talented React developer for building modern web applications. Experience with React, Redux, and RESTful APIs required.', 1, 2, 'Mumbai', '45000-70000', 'Full-time', 'Approved'),
('Digital Marketing Manager', 'Lead our digital marketing campaigns across multiple channels. Experience with SEO, SEM, and social media marketing essential.', 3, 3, 'Bangalore', '40000-60000', 'Full-time', 'Approved'),
('Data Entry Operator', 'Entry-level position for data entry into our ERP system. Good typing speed and attention to detail required.', 1, 2, 'Pune', '18000-25000', 'Full-time', 'Approved'),
('Content Writer', 'Create engaging content for blogs, social media, and marketing materials. Strong English writing skills required.', 3, 3, 'Remote', '25000-35000', 'Part-time', 'Approved'),
('Python Developer Intern', 'Internship opportunity for Python enthusiasts. Learn web development with Django/Flask frameworks.', 1, 2, 'Hyderabad', '15000-20000', 'Internship', 'Approved'),
('Graphic Designer', 'Design creative graphics for digital and print media. Proficiency in Adobe Creative Suite required.', 6, 3, 'Chennai', '30000-45000', 'Contract', 'Approved'),
('Sales Executive', 'Drive sales for our financial products. Prior experience in banking or insurance sales preferred.', 2, 3, 'Kolkata', '25000-40000', 'Full-time', 'Pending')
ON DUPLICATE KEY UPDATE title=VALUES(title);
