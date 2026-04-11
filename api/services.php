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