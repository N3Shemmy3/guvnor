<?php
require_once __DIR__ . '/../include/config.php';
requireAuth();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($method !== 'POST') respond(['success' => false, 'message' => 'POST required'], 405);

$data = getJson();
if (empty($data['email'])) respond(['success' => false, 'message' => 'email required'], 422);

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ?');
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

if (!$user) {
    respond(['success' => false, 'message' => 'Email not found'], 404);
}

$token = bin2hex(random_bytes(24));
$expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
$stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
$stmt->execute([$user['id'], $token, $expiresAt]);

$resetUrl = sprintf(
    'http%s://%s/api/auth/reset-password.php?token=%s',
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

respond(['success' => true, 'message' => 'Password reset link has been sent.']);
