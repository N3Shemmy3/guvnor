<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/admin-check.php';
require_once __DIR__ . '/../notifications.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list-applications':
        listApplications();
        break;

    case 'get-application':
        getApplication();
        break;

    case 'application-history':
        getApplicationHistory();
        break;

    case 'update-status':
        updateStatus($method);
        break;

    default:
        respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

function listApplications(): void
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('
        SELECT a.*, u.name AS user_name, u.email AS user_email, s.name AS service_name
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        ORDER BY a.submitted_at DESC
    ');
    respond(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

function getApplication(): void
{
    $applicationId = (int)($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) {
        respond(['success' => false, 'message' => 'application_id required'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT a.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
               s.name AS service_name, s.type AS service_type
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE a.id = ?
    ');
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$application) {
        respond(['success' => false, 'message' => 'Application not found'], 404);
    }

    respond([
        'success' => true,
        'data' => [
            'application' => $application,
            'history' => getApplicationHistoryRecords($applicationId),
        ]
    ]);
}

function getApplicationHistory(): void
{
    $applicationId = (int)($_GET['application_id'] ?? 0);
    if ($applicationId <= 0) {
        respond(['success' => false, 'message' => 'application_id required'], 422);
    }

    respond(['success' => true, 'data' => getApplicationHistoryRecords($applicationId)]);
}

function updateStatus(string $method): void
{
    if ($method !== 'POST') {
        respond(['success' => false, 'message' => 'POST required'], 405);
    }

    $data = getJson();
    if (empty($data['application_id']) || empty($data['status'])) {
        respond(['success' => false, 'message' => 'application_id and status required'], 422);
    }

    $allowed = ['pending', 'approved', 'rejected'];
    if (!in_array($data['status'], $allowed, true)) {
        respond(['success' => false, 'message' => 'Invalid status'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE applications SET status = ?, admin_comment = ? WHERE id = ?');
    $stmt->execute([$data['status'], $data['admin_comment'] ?? null, $data['application_id']]);

    appendApplicationHistory($data['application_id'], $data['status'], $data['admin_comment'] ?? null, 'admin');

    $stmt = $pdo->prepare('SELECT user_id, reference_no FROM applications WHERE id = ?');
    $stmt->execute([$data['application_id']]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        if ($data['status'] === 'approved') {
            createApprovalDocument($data['application_id']);
        }

        $message = "Your application {$application['reference_no']} is now {$data['status']}.";
        if ($data['status'] === 'approved') {
            $message .= ' Your approved document is available for download.';
        }

        queueNotification(
            $application['user_id'],
            $data['application_id'],
            'Application status updated',
            $message
        );
    }

    respond(['success' => true, 'message' => 'Application status updated']);
}

function createApprovalDocument(int $applicationId): ?string
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT a.reference_no, a.submitted_at, a.details, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
               s.name AS service_name, s.type AS service_type
        FROM applications a
        JOIN users u ON u.id = a.user_id
        JOIN services s ON s.id = a.service_id
        WHERE a.id = ?
    ');
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$application) {
        return null;
    }

    $uploadDir = __DIR__ . '/../approved_documents';
    @mkdir($uploadDir, 0755, true);

    $fileName = sprintf(
        'approved-%d-%s.html',
        $applicationId,
        preg_replace('/[^a-z0-9]+/', '-', strtolower($application['service_name']))
    );
    $filePath = $uploadDir . '/' . $fileName;

    $details = nl2br(htmlspecialchars($application['details'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $approvedAt = date('Y-m-d H:i:s');

    $html = <<<HTML
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Approved Document</title>
<style>
body { font-family: Arial, sans-serif; line-height: 1.4; }
.container { max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; }
h1 { margin-bottom: 0; }
.header { margin-bottom: 30px; }
.row { margin: 12px 0; }
.label { font-weight: bold; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Approved {$application['service_name']}</h1>
    <p>Reference: {$application['reference_no']}</p>
    <p>Approved on: {$approvedAt}</p>
  </div>
  <div class="row"><span class="label">Applicant:</span> {$application['user_name']}</div>
  <div class="row"><span class="label">Email:</span> {$application['user_email']}</div>
  <div class="row"><span class="label">Phone:</span> {$application['user_phone']}</div>
  <div class="row"><span class="label">Service:</span> {$application['service_name']}</div>
  <div class="row"><span class="label">Type:</span> {$application['service_type']}</div>
  <div class="row"><span class="label">Details:</span> {$details}</div>
  <div class="row"><span class="label">Status:</span> Approved</div>
</div>
</body>
</html>
HTML;

    file_put_contents($filePath, $html);

    $stmt = $pdo->prepare('UPDATE applications SET approved_document_path = ?, approved_at = NOW() WHERE id = ?');
    $stmt->execute(['approved_documents/' . basename($filePath), $applicationId]);

    return $filePath;
}
