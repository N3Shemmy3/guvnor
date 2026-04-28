<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/admin-check.php';
respond(['success' => getDbConnection() != null, 'message' => 'Where do you think you\'re going?'], 404);
