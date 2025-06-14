<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../middleware/auth.php';

$database = new Database();
$db = $database->connect();

$productController = new ProductController($db);

// Always parse URL path without query string
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];

        if (preg_match('/^\/api\/products\/category\/(\d+)$/', $requestPath, $matches)) {
            $productController->getByCategory($matches[1]);
        } else if (preg_match('/^\/api\/products\/(\d+)$/', $requestPath, $matches)) {
            $productController->getOne($matches[1]);
        } else {
            $productController->getAll();
        }
        break;

    case 'POST':
        requireSuperAdmin();
        $productController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/products\/(\d+)$/', $requestPath, $matches)) {
            requireSuperAdmin();
            $productController->delete($matches[1]);

        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid product ID in URL. Please ensure the ID is an integer.'
            ]);
        }
        break;

    case 'PUT':
        $matches = [];
        if (preg_match('/^\/api\/products\/(\d+)$/', $requestPath, $matches)) {
               requireSuperAdmin();
            $productController->update($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid product ID in URL'
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'status' => '405',
            'error' => 'Method not allowed. Please use GET, POST, PUT, or DELETE.'
        ]);
        break;
}
