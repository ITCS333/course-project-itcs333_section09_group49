<?php
require_once __DIR__ . "/../../../config/Config.php";

header('Content-Type: application/json');

echo json_encode([
    "isAdmin" => isAdmin(),
    "isLoggedIn" => isLoggedIn()
]);
