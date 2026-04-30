<?php
require_once __DIR__ . '/include/config.php';
require_once __DIR__ . '/notifications.php';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if (empty($action)) {
    $action = $_POST['action'] ?? '';
}

if (empty($action)) {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    $action = $rawInput['action'] ?? '';
}
switch ($action) {
    case 'submit-application':
        submitApplication($method);
        break;

    case 'my-applications':
        getMyApplications();
        break;

    case 'application-history':
        getApplicationHistoryEndpoint();
        break;

    case 'download-approved-document':
        downloadApprovedDocument($method);
        break;

    default:
        respond(['success' => false, 'message' => 'Invalid action'], 400);
}

function submitApplication($method)
{
    requireMethod($method, 'POST');
    $userId = requireAuth();

    $data = getJson();
    if (empty($data['service_id']) || empty($data['details'])) {
        respond(['success' => false, 'message' => 'service_id and details required'], 422);
    }

    $pdo = getDbConnection();
    $ref = 'APP-' . time() . '-' . rand(1000, 9999);
    $stmt = $pdo->prepare('INSERT INTO applications (user_id, service_id, reference_no, details) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $data['service_id'], $ref, $data['details']]);
    $applicationId = $pdo->lastInsertId();

    appendApplicationHistory($applicationId, 'pending', 'Application submitted by user.', 'user');
    queueNotification($userId, $applicationId, 'Application submitted', "Your application $ref has been received and is pending review.");

    respond(['success' => true, 'message' => 'Application submitted', 'reference_no' => $ref], 201);
}

function getMyApplications()
{
    $userId = requireAuth();

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT a.*, s.name AS service_name, s.type AS service_type
        FROM applications a
        JOIN services s ON a.service_id = s.id
        WHERE a.user_id = ?
        ORDER BY a.submitted_at DESC
    ');
    $stmt->execute([$userId]);
    $applications = $stmt->fetchAll();

    $accepted = $rejected = $pending = 0;
    foreach ($applications as $application) {
        match ($application['status']) {
            'approved' => $accepted++,
            'rejected' => $rejected++,
            default => $pending++
        };
    }

    respond([
        'success' => true,
        'data' => $applications,
        'accepted' => $accepted,
        'rejected' => $rejected,
        'pending' => $pending,
        'total' => count($applications),
    ]);
}

function getApplicationHistoryEndpoint()
{
    $userId = requireAuth();
    $applicationId = intval($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) respond(['success' => false, 'message' => 'application_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id FROM applications WHERE id = ? AND user_id = ?');
    $stmt->execute([$applicationId, $userId]);
    if (!$stmt->fetch()) {
        respond(['success' => false, 'message' => 'Application not found'], 404);
    }

    respond(['success' => true, 'data' => getApplicationHistoryRecords($applicationId)]);
}

function downloadApprovedDocument($method)
{
    requireMethod($method, 'GET');
    $userId = requireAuth();

    $applicationId = intval($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) respond(['success' => false, 'message' => 'application_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT approved_document_path FROM applications WHERE id = ? AND user_id = ?');
    $stmt->execute([$applicationId, $userId]);
    $application = $stmt->fetch();
    if (!$application || empty($application['approved_document_path'])) {
        respond(['success' => false, 'message' => 'No approved document available'], 404);
    }

    $filePath = __DIR__ . '/' . $application['approved_document_path'];
    if (!file_exists($filePath)) respond(['success' => false, 'message' => 'Document file not found'], 404);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

function appendApplicationHistory($applicationId, $status, $note = null, $createdBy = 'system')
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        INSERT INTO application_history (application_id, status, note, created_by)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$applicationId, $status, $note, $createdBy]);
}

function getApplicationHistoryRecords($applicationId)
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT id, status, note, created_by, created_at
        FROM application_history
        WHERE application_id = ?
        ORDER BY created_at ASC
    ');
    $stmt->execute([$applicationId]);
    return $stmt->fetchAll();
}
