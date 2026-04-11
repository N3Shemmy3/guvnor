<?php
header('Content-Type: application/json');
require_once __DIR__ . '/include/config.php';

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
    case 'signup':
        require_once __DIR__ . '/auth/signup.php';
        signupUser($method);
        break;

    case 'signin':
        require_once __DIR__ . '/auth/signin.php';
        signinUser($method);
        break;

    case 'signout':
        require_once __DIR__ . '/auth/signout.php';
        signoutUser();
        break;

    case 'request-password-reset':
        require_once __DIR__ . '/auth/request-password-reset.php';
        requestPasswordReset($method);
        break;

    case 'reset-password':
        require_once __DIR__ . '/auth/reset-password.php';
        resetPassword($method);
        break;

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

    case 'list-services':
    case 'get-service':
        require_once __DIR__ . '/services.php';
        if ($action === 'list-services') listServices();
        if ($action === 'get-service') getService();
        break;

    case 'user-notifications':
        require_once __DIR__ . '/notifications.php';
        getUserNotifications();
        break;

    case 'mark-notification-read':
        require_once __DIR__ . '/notifications.php';
        markNotificationRead($method);
        break;

    default:
        respond(['error' => 'Unknown action'], 404);
}
