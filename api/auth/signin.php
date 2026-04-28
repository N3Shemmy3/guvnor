<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';


if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['email']) || empty($data['password'])) {
    respond(['success' => false, 'message' => 'Email and password are required'], 422);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($data['password'], $user['password_hash'])) {
    respond(['success' => false, 'message' => 'Invalid email or password'], 401);
}

$token = bin2hex(random_bytes(32));
respond(['success' => true, 'message' => 'Sign-in successful', 'token' => $token], 200);
