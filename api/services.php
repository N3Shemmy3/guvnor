<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list-services':
        listServices();
        break;
    case 'get-service':
        getService();
        break;
    default:
        respond(['success' => false, 'message' => 'Method not allowed'], 405);
}

function listServices()
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id, service_code, name, description, type, created_at FROM services ORDER BY name ASC');
    respond(['success' => true, 'services' => $stmt->fetchAll()]);
}

function getService()
{
    $serviceId = intval($_GET['service_id'] ?? 0);
    if ($serviceId <= 0) respond(['success' => false, 'message' => 'service_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, service_code, name, description, type, created_at FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();
    if (!$service) respond(['success' => false, 'message' => 'Service not found'], 404);

    respond(['success' => true, 'message' => 'Service retrieved', 'data' => $service]);
}
