<?php

header('Content-Type: application/json');
$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (
    $http_origin === "http://localhost:3000"
    || $http_origin === "https://n3shemmy.icuprojects.icu"
    || $http_origin === "https://www.n3shemmy.icuprojects.icu"
) {
    header("Access-Control-Allow-Origin: $http_origin");
}

header("Access-Control-Allow-Methods: GET, DELETE, POST, PUT, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_secure', 'True');
    session_start();
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'None',
    ]);
}

function getDbConnection()
{
    $host = 'localhost';
    $db = 'n3shemmy_guvnor';
    $user = 'n3shemmy_guvnor';
    $pass = 'jHTJ2HCvdVQmCfX2C4jA';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        respond(['success' => false, 'message' => 'Database connection failed'], 500);
    }
}
