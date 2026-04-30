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
    respond(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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
    $userId = intval($data['user_id'] ?? 0);
    if ($userId <= 0) respond(['success' => false, 'message' => 'user_id required'], 422);

    $pdo = getDbConnection();

    try {
        $pdo->beginTransaction();

        // 1. Update Core User Table
        $stmtUser = $pdo->prepare('
            UPDATE users SET 
                name = ?, 
                email = ?, 
                phone = ?, 
                role = ?, 
                status = ? 
            WHERE id = ?
        ');
        $stmtUser->execute([
            $data['name'],
            $data['email'],
            $data['phone'] ?: null,
            $data['role'],
            $data['status'],
            $userId
        ]);

        // 2. Update or Insert User Profile
        // Using ON DUPLICATE KEY UPDATE ensures it works even if a profile doesn't exist yet
        $stmtProfile = $pdo->prepare('
            INSERT INTO user_profiles (user_id, nrc, address, gender, date_of_birth)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                nrc = VALUES(nrc),
                address = VALUES(address),
                gender = VALUES(gender),
                date_of_birth = VALUES(date_of_birth)
        ');
        $stmtProfile->execute([
            $userId,
            $data['nrc'] ?: '',
            $data['address'] ?: null,
            $data['gender'] ?: null,
            $data['date_of_birth'] ?: null
        ]);

        $pdo->commit();
        respond(['success' => true, 'message' => 'Profile and account updated']);
    } catch (Exception $e) {
        $pdo->rollBack();
        respond(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}
