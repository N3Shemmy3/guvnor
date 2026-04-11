<?php
function listUsers()
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('
        SELECT u.id, u.name, u.email, u.phone, u.role, u.status, u.created_at,
               p.nrc, p.address, p.gender, p.date_of_birth
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        ORDER BY u.created_at DESC
    ');
    respond(['users' => $stmt->fetchAll()]);
}

function getUser()
{
    $userId = intval($_GET['user_id'] ?? 0);
    if ($userId <= 0) respond(['error' => 'user_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT u.id, u.name, u.email, u.phone, u.role, u.status, u.created_at,
               p.nrc, p.address, p.gender, p.date_of_birth
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = ?
    ');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) respond(['error' => 'User not found'], 404);

    respond(['user' => $user]);
}

function updateUser($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['user_id'])) respond(['error' => 'user_id required'], 422);

    $allowedRoles = ['user', 'admin'];
    $allowedStatus = ['active', 'inactive'];

    $role = $data['role'] ?? null;
    $status = $data['status'] ?? null;

    if ($role !== null && !in_array($role, $allowedRoles, true)) {
        respond(['error' => 'Invalid role'], 422);
    }
    if ($status !== null && !in_array($status, $allowedStatus, true)) {
        respond(['error' => 'Invalid status'], 422);
    }

    $fields = [];
    $params = [];
    if ($role !== null) {
        $fields[] = 'role = ?';
        $params[] = $role;
    }
    if ($status !== null) {
        $fields[] = 'status = ?';
        $params[] = $status;
    }
    if (empty($fields)) {
        respond(['error' => 'No update fields provided'], 422);
    }

    $params[] = $data['user_id'];
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?');
    $stmt->execute($params);

    respond(['message' => 'User updated']);
}