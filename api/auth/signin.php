<?php
function signinUser($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['email']) || empty($data['password'])) {
        respond(['error' => 'Email and password required'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, password_hash, role, status FROM users WHERE email = ?');
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($data['password'], $user['password_hash'])) {
        respond(['error' => 'Invalid credentials'], 401);
    }
    if ($user['status'] !== 'active') {
        respond(['error' => 'Account inactive'], 403);
    }

    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    respond(['message' => 'Login successful', 'role' => $user['role']]);
}