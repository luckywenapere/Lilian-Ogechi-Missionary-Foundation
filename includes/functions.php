<?php
// includes/functions.php

require_once 'config.php';

// Function to get all events
function getEvents($status = 'published', $limit = null) {
    global $pdo;
    
    $sql = "SELECT c.*, u.username as author_name 
            FROM content c 
            LEFT JOIN users u ON c.author_id = u.id 
            WHERE c.content_type = 'event' AND c.status = ? 
            ORDER BY c.event_date ASC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

// Function to get single event by ID
function getEventById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.username as author_name 
        FROM content c 
        LEFT JOIN users u ON c.author_id = u.id 
        WHERE c.id = ? AND c.content_type = 'event'
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Function to format date for display
function formatEventDate($date) {
    return date('F j, Y', strtotime($date));
}

// Function to handle file uploads
function uploadImage($file, $type = 'events') {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Only JPG, PNG, and GIF images are allowed.');
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        throw new Exception('File size must be less than 5MB.');
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = UPLOAD_PATH . $type . '/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to move uploaded file.');
    }
    
    return $filename;
}

// Function to delete image file
function deleteImage($filename, $type = 'events') {
    $file_path = UPLOAD_PATH . $type . '/' . $filename;
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

// Sanitize output
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}