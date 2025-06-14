<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ContactDetailsController.php';

$database = new Database();
$db = $database->connect();

$controller = new ContactDetailsController($db);
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        $controller->get();
        break;

    case 'PUT':
        requireSuperAdmin();
        $controller->update();
        break;

    case 'DELETE':
        requireSuperAdmin();
        $controller->delete();
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 405, 'message' => 'Method not allowed']);
        break;
}
