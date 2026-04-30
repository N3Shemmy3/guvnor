<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/admin-check.php';

// Parse JSON input immediately to handle Axios POST requests
$jsonData = getJson();
$method = $_SERVER['REQUEST_METHOD'];

// Extract action from JSON, POST, or GET
$action = $jsonData['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list-services':
        listServices();
        break;

    case 'get-service':
        getService();
        break;

    case 'create-service':
        createService($method, $jsonData);
        break;

    case 'update-service':
        updateService($method, $jsonData);
        break;

    case 'delete-service':
        deleteService($method, $jsonData);
        break;

    default:
        respond(['success' => false, 'message' => 'Invalid action: ' . $action], 400);
}

function listServices()
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id, service_code, name, description, type, created_at FROM services ORDER BY name ASC');
    respond(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

function getService()
{
    $serviceId = intval($_GET['service_id'] ?? 0);
    if ($serviceId <= 0) respond(['success' => false, 'message' => 'service_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, service_code, name, description, type, created_at FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$service) respond(['success' => false, 'message' => 'Service not found'], 404);

    respond(['success' => true, 'data' => $service]);
}

function createService($method, $data)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    if (empty($data['service_code']) || empty($data['name']) || empty($data['type'])) {
        respond(['success' => false, 'message' => 'service_code, name, and type are required'], 422);
    }

    $allowedTypes = ['permit', 'certificate', 'registration'];
    if (!in_array($data['type'], $allowedTypes, true)) {
        respond(['success' => false, 'message' => 'Invalid service type'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO services (service_code, name, description, type) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['service_code'],
        $data['name'],
        $data['description'] ?? null,
        $data['type']
    ]);

    respond(['success' => true, 'message' => 'Service created', 'service_id' => $pdo->lastInsertId()], 201);
}

function updateService($method, $data)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    if (empty($data['service_id'])) respond(['success' => false, 'message' => 'service_id required'], 422);

    $fields = [];
    $params = [];

    if (!empty($data['service_code'])) {
        $fields[] = 'service_code = ?';
        $params[] = $data['service_code'];
    }
    if (!empty($data['name'])) {
        $fields[] = 'name = ?';
        $params[] = $data['name'];
    }
    if (array_key_exists('description', $data)) {
        $fields[] = 'description = ?';
        $params[] = $data['description'];
    }
    if (!empty($data['type'])) {
        $allowedTypes = ['permit', 'certificate', 'registration'];
        if (!in_array($data['type'], $allowedTypes, true)) {
            respond(['success' => false, 'message' => 'Invalid service type'], 422);
        }
        $fields[] = 'type = ?';
        $params[] = $data['type'];
    }

    if (empty($fields)) {
        respond(['success' => false, 'message' => 'No fields to update'], 422);
    }

    $params[] = $data['service_id'];
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE services SET ' . implode(', ', $fields) . ' WHERE id = ?');
    $stmt->execute($params);

    respond(['success' => true, 'message' => 'Service updated']);
}

function deleteService($method, $data)
{
    if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

    if (empty($data['service_id'])) respond(['success' => false, 'message' => 'service_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$data['service_id']]);

    respond(['success' => true, 'message' => 'Service deleted']);
}
