<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/admin-check.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list-users':
        listUsers();
        break;
    case 'get-user':
        getUser();
        break;
    case 'update-user':
        updateUser($method);
        break;
    default:
        respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

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
    respond(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getUser()
{
    $userId = intval($_GET['user_id'] ?? 0);
    if ($userId <= 0) respond(['success' => false, 'message' => 'user_id required'], 422);

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
    if (!$user) respond(['success' => false, 'message' => 'User not found'], 404);

    respond(['success' => true, 'data' => $user]);
}

function updateUser($method)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['user_id'])) respond(['success' => false, 'message' => 'user_id required'], 422);

    $allowedRoles = ['user', 'admin'];
    $allowedStatus = ['active', 'inactive'];

    $role = $data['role'] ?? null;
    $status = $data['status'] ?? null;

    if ($role !== null && !in_array($role, $allowedRoles, true)) {
        respond(['success' => false, 'message' => 'Invalid role'], 422);
    }
    if ($status !== null && !in_array($status, $allowedStatus, true)) {
        respond(['success' => false, 'message' => 'Invalid status'], 422);
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
        respond(['success' => false, 'message' => 'No update fields provided'], 422);
    }

    $params[] = $data['user_id'];
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?');
    $stmt->execute($params);

    respond(['success' => true, 'message' => 'User updated']);
}
