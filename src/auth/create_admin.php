<?php
include '../../config/Config.php';

// NEW ADMIN INFO
$username = "admin3";
$email = "admin3@example.com";
$password = "password";   // plain text
$role = "admin";

// HASH PASSWORD
$hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password_hash, role, is_active)
        VALUES (?, ?, ?, ?, 1)
    ");
    $stmt->execute([$username, $email, $hashed, $role]);

    echo "<h1>SUCCESS!</h1>";
    echo "<p>New admin created:</p>";
    echo "<pre>";
    echo "username: $username\n";
    echo "password: $password\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "<h1>ERROR</h1>";
    echo $e->getMessage();
}
?>
