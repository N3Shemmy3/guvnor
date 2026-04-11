<?php
function listServices()
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id, service_code, name, description, type, created_at FROM services ORDER BY name ASC');
    respond(['services' => $stmt->fetchAll()]);
}

function getService()
{
    $serviceId = intval($_GET['service_id'] ?? 0);
    if ($serviceId <= 0) respond(['error' => 'service_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, service_code, name, description, type, created_at FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();
    if (!$service) respond(['error' => 'Service not found'], 404);

    respond(['service' => $service]);
}

function createService($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['service_code']) || empty($data['name']) || empty($data['type'])) {
        respond(['error' => 'service_code, name, and type are required'], 422);
    }

    $allowedTypes = ['permit', 'certificate', 'registration'];
    if (!in_array($data['type'], $allowedTypes, true)) {
        respond(['error' => 'Invalid service type'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO services (service_code, name, description, type) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['service_code'],
        $data['name'],
        $data['description'] ?? null,
        $data['type']
    ]);

    respond(['message' => 'Service created', 'service_id' => $pdo->lastInsertId()], 201);
}

function updateService($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['service_id'])) respond(['error' => 'service_id required'], 422);

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
            respond(['error' => 'Invalid service type'], 422);
        }
        $fields[] = 'type = ?';
        $params[] = $data['type'];
    }

    if (empty($fields)) {
        respond(['error' => 'No fields to update'], 422);
    }

    $params[] = $data['service_id'];
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE services SET ' . implode(', ', $fields) . ' WHERE id = ?');
    $stmt->execute($params);

    respond(['message' => 'Service updated']);
}

function deleteService($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['service_id'])) respond(['error' => 'service_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$data['service_id']]);

    respond(['message' => 'Service deleted']);
}