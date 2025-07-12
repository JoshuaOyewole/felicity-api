<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

$database = new Database();
$db = $database->connect();

$controller = new DashboardController($db);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
    $controller->getStats();
} else {
    http_response_code(405);
    echo json_encode(['status' => 405, 'message' => 'Method not allowed']);
}
