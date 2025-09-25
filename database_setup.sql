-- Create the database
CREATE DATABASE IF NOT EXISTS refugee_training_db;
USE refugee_training_db;

-- Create administrators table
CREATE TABLE IF NOT EXISTS administrators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create applicants table
CREATE TABLE IF NOT EXISTS applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    language VARCHAR(30) NOT NULL,
    program VARCHAR(50) NOT NULL,
    education_level VARCHAR(50) NOT NULL,
    id_document_path VARCHAR(255) NOT NULL,
    academic_document_path VARCHAR(255) NOT NULL,
    background TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a sample admin (password: admin123)
INSERT INTO administrators (name, email, password) 
VALUES ('Admin User', 'admin@bsu.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');