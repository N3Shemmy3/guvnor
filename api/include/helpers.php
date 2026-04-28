<?php

function respond($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getJson()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function requireAuth()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        respond(['success' => false, 'message' => 'Authentication required'], 401);
    }
    return $_SESSION['user_id'];
}

function requireMethod($method, $allowed = ['POST'])
{
    if (!in_array($method, (array)$allowed)) {
        respond(['success' => false, 'message' => ucfirst(strtolower(implode('/', $allowed))) . ' required'], 405);
    }
}
