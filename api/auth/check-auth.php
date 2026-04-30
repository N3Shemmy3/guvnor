<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../include/config.php';


if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'success' => true,
        'name' => $_SESSION['name'] ?? 'Citizen User',
        'role' => $_SESSION['role'] ?? 'user',
        'user_id' => $_SESSION['user_id']
    ]);
} else {
    echo json_encode([
        'loggedIn' => false,
        'success' => false,
        'message' => 'Not authenticated'
    ]);
}
