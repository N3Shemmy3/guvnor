<?php
function resetPassword($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['token']) || empty($data['password'])) {
        respond(['error' => 'token and password required'], 422);
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, user_id, expires_at, used FROM password_resets WHERE token = ?');
    $stmt->execute([$data['token']]);
    $reset = $stmt->fetch();

    if (!$reset || $reset['used'] || strtotime($reset['expires_at']) < time()) {
        respond(['error' => 'Invalid or expired token'], 400);
    }

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([$passwordHash, $reset['user_id']]);

    $stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
    $stmt->execute([$reset['id']]);

    respond(['message' => 'Password reset successful']);
}
