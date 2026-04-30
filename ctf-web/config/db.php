<?php
// File: config/db.php
// Database Configuration

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosong untuk XAMPP default
define('DB_NAME', 'ctf_platform');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function untuk cek admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Helper function untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}
?>