<?php
function requestPasswordReset($method)
{
    if ($method !== 'POST') respond(['error' => 'POST required'], 405);

    $data = getJson();
    if (empty($data['email'])) respond(['error' => 'email required'], 422);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ?');
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(24));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $token, $expiresAt]);

        $resetUrl = sprintf(
            'http%s://%s/api/index.php?action=reset-password&token=%s',
            !empty($_SERVER['HTTPS']) ? 's' : '',
            $_SERVER['HTTP_HOST'],
            $token
        );

        $subject = 'Password reset request';
        $message = "Hello {$user['name']},\n\n";
        $message .= "We received a password reset request for your account.\n\n";
        $message .= "Use the link below to reset your password. The link expires in 1 hour.\n\n";
        $message .= $resetUrl . "\n\n";
        $message .= "If you did not request this, you can ignore this email.\n";

        $headers = "From: no-reply@guvnor.local\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        @mail($data['email'], $subject, $message, $headers);
    }

    respond(['message' => 'If that email exists, a password reset link has been sent.']);
}