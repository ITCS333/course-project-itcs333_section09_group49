<?php
require_once __DIR__ . '../../../../config/Config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

echo json_encode([
    'userId'   => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'isAdmin'  => function_exists('isAdmin') ? isAdmin() : false
]);
