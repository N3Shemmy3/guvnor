<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'user-notifications':
        getUserNotifications();
        break;

    case 'mark-notification-read':
        markNotificationRead($method);
        break;

    default:
        respond(['success' => false, 'message' => 'Invalid action'], 400);
}

function getUserNotifications()
{
    $userId = requireAuth();
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT id, application_id, title, message, type, is_read, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC
    ');
    $stmt->execute([$userId]);
    respond(['success' => true, 'notifications' => $stmt->fetchAll()]);
}

function markNotificationRead($method)
{
    requireMethod($method, 'POST');
    $userId = requireAuth();

    $data = getJson();
    if (empty($data['notification_id'])) respond(['success' => false, 'message' => 'notification_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$data['notification_id'], $userId]);

    respond(['success' => true, 'message' => 'Notification marked as read']);
}

function queueNotification($userId, $applicationId, $title, $message, $type = 'status')
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        INSERT INTO notifications (user_id, application_id, title, message, type)
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmt->execute([$userId, $applicationId, $title, $message, $type]);

    $stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        sendEmailNotification($user['email'], $title, $message);
    }
}

function sendEmailNotification($to, $subject, $body)
{
    $from = 'no-reply@guvnor.local';
    $headers = "From: Guvnor Notifications <$from>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    @mail($to, $subject, $body, $headers);
}
