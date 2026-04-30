<?php
require_once __DIR__ . '/../include/config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['email']) || empty($data['password'])) {
    respond(['success' => false, 'message' => 'Email and password are required'], 422);
}

$pdo = getDbConnection();
// UPDATE: Added 'name' and 'role' to the SELECT statement
$stmt = $pdo->prepare('SELECT id, name, role, password_hash FROM users WHERE email = ?');
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    respond(['success' => false, 'message' => 'Email not registered'], 401);
}

if (!password_verify($data['password'], $user['password_hash'])) {
    respond(['success' => false, 'message' => 'Incorrect password'], 401);
}

// Now these will work because 'name' and 'role' are in the $user array
$_SESSION['user_id'] = $user['id'];
$_SESSION['logged_in'] = true;
$_SESSION['name'] = $user['name'] ?? 'User';
$_SESSION['role'] = $user['role'] ?? 'user';

// Note: If you're using sessions, you don't strictly need this token 
// unless your frontend is specifically looking for it in localStorage.
$token = bin2hex(random_bytes(32));

respond(['success' => true, 'message' => 'Sign-in successful', 'token' => $token], 200);
