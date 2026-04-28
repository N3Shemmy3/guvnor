<?php
require_once __DIR__ . '/../include/config.php';
requireAuth();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method !== 'POST')
    respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['token']) || empty($data['password'])) {
    respond(['success' => false, 'message' => 'token and password required'], 422);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id, user_id, expires_at, used FROM password_resets WHERE token = ?');
$stmt->execute([$data['token']]);
$reset = $stmt->fetch();

if (!$reset || $reset['used'] || strtotime($reset['expires_at']) < time()) {
    respond(['success' => false, 'message' => 'Invalid or expired token'], 400);
}

$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$stmt->execute([$passwordHash, $reset['user_id']]);

$stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
$stmt->execute([$reset['id']]);

respond(['success' => true, 'message' => 'Password reset successful']);
