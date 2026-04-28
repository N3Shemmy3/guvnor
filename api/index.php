<?php
require_once __DIR__ . '/include/config.php';


$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

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


    case 'save-profile':
        require_once __DIR__ . '/users.php';
        saveUserProfile($method);
        break;

    case 'get-profile':
        require_once __DIR__ . '/users.php';
        getUserProfile();
        break;

    case 'upload-document':
        require_once __DIR__ . '/users.php';
        uploadUserDocument($method);
        break;

    case 'user-documents':
        require_once __DIR__ . '/users.php';
        listUserDocuments();
        break;

    case 'submit-application':
        require_once __DIR__ . '/applications.php';
        submitApplication($method);
        break;

    case 'my-applications':
        require_once __DIR__ . '/applications.php';
        getMyApplications();
        break;

    case 'download-approved-document':
        require_once __DIR__ . '/applications.php';
        downloadApprovedDocument($method);
        break;

    case 'application-history':
        require_once __DIR__ . '/applications.php';
        getApplicationHistoryEndpoint();
        break;



    default:
        respond(['error' => 'Unknown action'], 404);
}
