<?php
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 1800); 

session_start();

$host = "localhost";
$user = "root"; 
$password = "";
$database = "course_page";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}

function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    elseif (isset($_COOKIE['user_id']) && isset($_COOKIE['username']) && isset($_COOKIE['role'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['role'] = $_COOKIE['role'];
        return true;
    }
    return false;
}

function isAdmin() {
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'instructor');
}
?>
