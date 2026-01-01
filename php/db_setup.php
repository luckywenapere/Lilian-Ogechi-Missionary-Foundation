<?php
// php/db_setup.php

// Include configuration
require_once '../includes/config.php';

function createDatabase($pdo) {
    try {
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        
        echo "Database created successfully!<br>";
        return true;
    } catch (PDOException $e) {
        die("Error creating database: " . $e->getMessage());
    }
}

function createTables($pdo) {
    try {
        // Users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('admin', 'editor') DEFAULT 'editor',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_active BOOLEAN DEFAULT TRUE
            )
        ");
        echo "Users table created successfully!<br>";

        // Content table (for events, news, programs)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS content (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                content_type ENUM('event', 'news', 'program') NOT NULL,
                description TEXT,
                content_body LONGTEXT,
                featured_image VARCHAR(255),
                event_date DATE NULL,
                event_location VARCHAR(255) NULL,
                status ENUM('published', 'draft') DEFAULT 'draft',
                author_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
        echo "Content table created successfully!<br>";

        // Gallery images table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS gallery_images (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255),
                image_path VARCHAR(255) NOT NULL,
                description TEXT,
                content_id INT NULL,
                uploaded_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE SET NULL
            )
        ");
        echo "Gallery images table created successfully!<br>";

        return true;

    } catch (PDOException $e) {
        die("Error creating tables: " . $e->getMessage());
    }
}

function createDefaultAdmin($pdo) {
    try {
        // Check if admin already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Create default admin user (password: admin123)
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, role, is_active) 
                VALUES (?, ?, ?, 'admin', TRUE)
            ");
            $stmt->execute(['admin', 'admin@foundation.org', $hashed_password]);
            
            echo "Default admin user created!<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
            echo "<strong>IMPORTANT: Change this password immediately after login!</strong><br>";
        } else {
            echo "Admin user already exists.<br>";
        }
        
        return true;
    } catch (PDOException $e) {
        die("Error creating admin user: " . $e->getMessage());
    }
}

// Main execution
echo "<h2>Setting up Charity Foundation Database</h2>";

try {
    // First connect without database to create it
    $temp_pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    createDatabase($temp_pdo);
    
    // Now reconnect with the database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    createTables($pdo);
    createDefaultAdmin($pdo);
    
    echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
    echo "<p><a href='../admin/login.php'>Go to Admin Login</a></p>";
    
} catch (PDOException $e) {
    die("<h3 style='color: red;'>Setup failed: " . $e->getMessage() . "</h3>");
}
?>