<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    respond(['success' => false, 'message' => 'name, email, password required'], 422);
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    respond(['success' => false, 'message' => 'Email already registered'], 409);
}

$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, phone) VALUES (?, ?, ?, ?)');
$stmt->execute([
    $data['name'],
    $data['email'],
    $passwordHash,
    $data['phone'] ?? null
]);

respond(['success' => true, 'message' => 'Registration successful'], 201);
