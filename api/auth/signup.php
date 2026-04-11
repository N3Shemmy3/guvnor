<?php
function signupUser($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        respond(['error' => 'name, email, password required'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        respond(['error' => 'Email already registered'], 409);
    }

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, phone) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['name'],
        $data['email'],
        $passwordHash,
        $data['phone'] ?? null
    ]);

    respond(['message' => 'Registration successful'], 201);
}