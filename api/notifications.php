<?php
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

function appendApplicationHistory($applicationId, $status, $note = null, $createdBy = 'system')
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        INSERT INTO application_history (application_id, status, note, created_by)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$applicationId, $status, $note, $createdBy]);
}

function getApplicationHistoryRecords($applicationId)
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT id, status, note, created_by, created_at
        FROM application_history
        WHERE application_id = ?
        ORDER BY created_at ASC
    ');
    $stmt->execute([$applicationId]);
    return $stmt->fetchAll();
}

function getUserNotifications()
{
    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('
        SELECT id, application_id, title, message, type, is_read, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    respond(['notifications' => $stmt->fetchAll()]);
}

function markNotificationRead($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    session_start();
    if (empty($_SESSION['user_id'])) respond(['error' => 'Authentication required'], 401);

    $data = getJson();
    if (empty($data['notification_id'])) respond(['error' => 'notification_id required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$data['notification_id'], $_SESSION['user_id']]);

    respond(['message' => 'Notification marked as read']);
}