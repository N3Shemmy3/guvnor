<?php
require_once __DIR__ . '/../include/config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['email']) || empty($data['password'])) {
    respond(['success' => false, 'message' => 'Email and password are required'], 422);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    respond(['success' => false, 'message' => 'Email not registered'], 401);
}

if (!password_verify($data['password'], $user['password_hash'])) {
    respond(['success' => false, 'message' => 'Incorrect password'], 401);
}

$_SESSION['user_id'] = $user['id'];
$token = bin2hex(random_bytes(32));
respond(['success' => true, 'message' => 'Sign-in successful', 'token' => $token], 200);
