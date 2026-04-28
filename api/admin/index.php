<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/admin-check.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function respond($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getJson()
{
    return json_decode(file_get_contents('php://input'), true) ?: [];
}

switch ($action) {
    case 'list-users':
    case 'get-user':
    case 'update-user':
        require_once __DIR__ . '/users.php';
        if ($action === 'list-users') listUsers();
        if ($action === 'get-user') getUser();
        if ($action === 'update-user') updateUser($method);
        break;

    case 'list-applications':
    case 'get-application':
    case 'update-status':
    case 'application-history':
        require_once __DIR__ . '/applications.php';
        if ($action === 'list-applications') listApplications();
        if ($action === 'get-application') getApplication();
        if ($action === 'update-status') updateStatus($method);
        if ($action === 'application-history') getApplicationHistory();
        break;

    case 'list-services':
    case 'get-service':
    case 'create-service':
    case 'update-service':
    case 'delete-service':
        require_once __DIR__ . '/services.php';
        if ($action === 'list-services') listServices();
        if ($action === 'get-service') getService();
        if ($action === 'create-service') createService($method);
        if ($action === 'update-service') updateService($method);
        if ($action === 'delete-service') deleteService($method);
        break;

    default:
        respond(['error' => 'Unknown action'], 404);
}
