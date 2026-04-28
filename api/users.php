<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'save-profile':
        saveUserProfile($method);
        break;
    case 'get-profile':
        getUserProfile();
        break;
    case 'upload-document':
        uploadUserDocument($method);
        break;
    case 'list-documents':
        listUserDocuments();
        break;
    default:
        respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

function respond(array $payload, int $status = 200)
{
    http_response_code($status);
    if (!array_key_exists('success', $payload)) {
        $payload['success'] = $status >= 200 && $status < 300;
    }
    echo json_encode($payload);
    exit;
}

function getJson()
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return is_array($data) ? $data : [];
}

function saveUserProfile($method)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    session_start();
    if (empty($_SESSION['user_id'])) respond(['success' => false, 'message' => 'Authentication required'], 401);

    $data = getJson();
    if (empty($data['full_name']) || empty($data['nrc'])) {
        respond(['success' => false, 'message' => 'full_name and nrc are required'], 422);
    }

    $pdo = getDbConnection();
    if (!empty($data['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$data['email'], $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            respond(['success' => false, 'message' => 'Email already in use'], 409);
        }
    }

    $updateFields = ['name = ?', 'phone = ?'];
    $params = [$data['full_name'], $data['phone'] ?? null];
    if (!empty($data['email'])) {
        $updateFields[] = 'email = ?';
        $params[] = $data['email'];
    }
    $params[] = $_SESSION['user_id'];

    $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ?');
    $stmt->execute($params);

    $stmt = $pdo->prepare('SELECT user_id FROM user_profiles WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare('
            UPDATE user_profiles
            SET nrc = ?, address = ?, gender = ?, date_of_birth = ?
            WHERE user_id = ?
        ');
        $stmt->execute([
            $data['nrc'],
            $data['address'] ?? null,
            $data['gender'] ?? null,
            $data['date_of_birth'] ?? null,
            $_SESSION['user_id']
        ]);
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO user_profiles (user_id, nrc, address, gender, date_of_birth)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $_SESSION['user_id'],
            $data['nrc'],
            $data['address'] ?? null,
            $data['gender'] ?? null,
            $data['date_of_birth'] ?? null
        ]);
    }

    respond(['success' => true, 'message' => 'Profile saved']);
}

function getUserProfile()
{
    session_start();
    if (empty($_SESSION['user_id'])) respond(['success' => false, 'message' => 'Authentication required'], 401);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT u.id, u.name AS full_name, u.email, u.phone,
               p.nrc, p.address, p.gender, p.date_of_birth
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = ?
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch();

    if (!$profile) respond(['success' => false, 'message' => 'Profile not found'], 404);
    respond(['success' => true, 'message' => 'Profile found', 'data' => $profile]);
}

function uploadUserDocument($method)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    session_start();
    if (empty($_SESSION['user_id'])) respond(['success' => false, 'message' => 'Authentication required'], 401);

    if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        respond(['success' => false, 'message' => 'Document upload failed'], 400);
    }

    $documentType = $_POST['document_type'] ?? 'general';
    $uploadDir = __DIR__ . '/uploads/documents';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $originalName = basename($_FILES['document']['name']);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $fileName = sprintf('%s_%s.%s', time(), bin2hex(random_bytes(6)), $extension);
    $destination = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
        respond(['success' => false, 'message' => 'Unable to move uploaded file'], 500);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        INSERT INTO user_documents (user_id, document_type, original_name, file_name, file_path, uploaded_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([
        $_SESSION['user_id'],
        $documentType,
        $originalName,
        $fileName,
        'uploads/documents/' . $fileName
    ]);

    respond([
        'success' => true,
        'message' => 'Document uploaded',
        'data' => ['file_path' => 'uploads/documents/' . $fileName]
    ], 201);
}

function listUserDocuments()
{
    session_start();
    if (empty($_SESSION['user_id'])) respond(['success' => false, 'message' => 'Authentication required'], 401);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT id, document_type, original_name, file_name, file_path, uploaded_at
        FROM user_documents
        WHERE user_id = ?
        ORDER BY uploaded_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $documents = $stmt->fetchAll();

    respond(['success' => true, 'message' => 'Documents retrieved', 'data' => $documents]);
}
