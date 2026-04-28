<?php
require_once __DIR__ . '/include/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

session_start();
session_destroy();
respond(['success' => true, 'message' => 'Logged out'], 200);
