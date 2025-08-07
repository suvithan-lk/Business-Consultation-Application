-- Create database
CREATE DATABASE IF NOT EXISTS consultation_db;
USE consultation_db;

-- Create the consultation applications table
CREATE TABLE consultation_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    business_name VARCHAR(100),
    website VARCHAR(255),
    location VARCHAR(100),
    business_stage ENUM('just_idea', 'newly_launched', 'few_months', 'over_1_year') NOT NULL,
    business_type TEXT NOT NULL,
    has_logo ENUM('yes', 'no', 'need_help'),
    online_presence TEXT,
    website_type ENUM('portfolio', 'business_info', 'ecommerce', 'blog', 'not_sure'),
    consultation_goals TEXT NOT NULL,
    budget_range ENUM('under_500', '500_1000', '1000_2500', '2500_5000', '5000_plus', 'discuss'),
    support_needs TEXT,
    preferred_datetime DATETIME,
    contact_method ENUM('whatsapp', 'zoom', 'phone', 'email') NOT NULL,
    additional_details TEXT,
    consent_agreed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('new', 'contacted', 'scheduled', 'completed', 'cancelled') DEFAULT 'new',
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status)
);

-- Create admin users table (optional - for managing submissions)
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager') DEFAULT 'manager',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert a default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (username, email, password_hash, role) 
VALUES ('admin', 'admin@yourcompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Create a view for easy data retrieval
CREATE VIEW consultation_summary AS
SELECT 
    id,
    full_name,
    email,
    phone,
    business_name,
    business_stage,
    consultation_goals,
    budget_range,
    contact_method,
    status,
    created_at
FROM consultation_applications
ORDER BY created_at DESC;