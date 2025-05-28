<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$database = new Database();
$db = $database->connect();

$productController = new ProductController($db);


// Assuming the $productController is already instantiated somewhere earlier in the code
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];
        // Check if URL matches /api/products/category/{categoryId}
        if (preg_match('/^\/api\/products\/category\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $productController->getByCategory($matches[1]);
        }
        // Check if URL matches /api/products/{id}
        else if (preg_match('/^\/api\/products\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $productController->getOne($matches[1]);
        } else {
            $productController->getAll();
        }
        break;
       

    case 'POST':
        $productController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/products\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
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
        if (preg_match('/^\/api\/products\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $id = $matches[1];
            $productController->update($id);
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


?>