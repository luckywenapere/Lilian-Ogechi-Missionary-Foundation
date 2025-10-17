<?php
// includes/config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'charity_foundation');
define('DB_USER', 'root');  // Change this to MySQL username
define('DB_PASS', '');      // Change this to MySQL password

// Site configuration
define('SITE_URL', 'http://localhost/lilian-ogechi-foundation'); // Change to domain
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_PATH . 'events/')) {
    mkdir(UPLOAD_PATH . 'events/', 0777, true);
}
if (!file_exists(UPLOAD_PATH . 'gallery/')) {
    mkdir(UPLOAD_PATH . 'gallery/', 0777, true);
}
?>