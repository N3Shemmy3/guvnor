<?php
require_once __DIR__ . '/notifications.php';

function submitApplication($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $data = getJson();
    if (empty($data['service_id']) || empty($data['details'])) {
        respond(['error' => 'service_id and details required'], 422);
    }

    $pdo = getDbConnection();
    $ref = 'APP-' . time() . '-' . rand(1000, 9999);
    $stmt = $pdo->prepare('INSERT INTO applications (user_id, service_id, reference_no, details) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $data['service_id'], $ref, $data['details']]);
    $applicationId = $pdo->lastInsertId();

    appendApplicationHistory($applicationId, 'pending', 'Application submitted by user.', 'user');

    queueNotification(
        $_SESSION['user_id'],
        $applicationId,
        'Application submitted',
        "Your application $ref has been received and is pending review."
    );

    respond(['message' => 'Application submitted', 'reference_no' => $ref], 201);
}

function getMyApplications()
{
    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT a.*, s.name AS service_name, s.type AS service_type
        FROM applications a
        JOIN services s ON a.service_id = s.id
        WHERE a.user_id = ?
        ORDER BY a.submitted_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();

    $accepted = $rejected = $pending = 0;
    foreach ($applications as $application) {
        if ($application['status'] === 'approved') {
            $accepted++;
        } elseif ($application['status'] === 'rejected') {
            $rejected++;
        } else {
            $pending++;
        }
    }

    respond([
        'applications' => $applications,
        'accepted_count' => $accepted,
        'rejected_count' => $rejected,
        'pending_count' => $pending,
        'total_count' => count($applications),
    ]);
}

function getApplicationHistoryEndpoint()
{
    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $applicationId = intval($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) respond(['error' => 'application_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id FROM applications WHERE id = ? AND user_id = ?');
    $stmt->execute([$applicationId, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        respond(['error' => 'Application not found or not owned by user'], 404);
    }

    respond(['history' => getApplicationHistoryRecords($applicationId)]);
}

function downloadApprovedDocument($method)
{
    if ($method !== 'GET') respond(['error' => 'GET required'], 405);

    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $applicationId = intval($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) respond(['error' => 'application_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT approved_document_path FROM applications WHERE id = ? AND user_id = ?');
    $stmt->execute([$applicationId, $_SESSION['user_id']]);
    $application = $stmt->fetch();
    if (!$application || empty($application['approved_document_path'])) {
        respond(['error' => 'No approved document available'], 404);
    }

    $filePath = __DIR__ . '/' . $application['approved_document_path'];
    if (!file_exists($filePath)) respond(['error' => 'Document file not found'], 404);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}