<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/OrderController.php';

$database = new Database();
$db = $database->connect();

$ordercontroller = new OrderRequestController($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
         $matches = [];
       
        // Check if URL matches /api/orders/{id}
        if (preg_match('/^\/api\/orders\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $ordercontroller->getOne($matches[1]);
        } else {
            $ordercontroller->getAll();
        }
        break;

    case 'POST':
        $ordercontroller->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/orders\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $ordercontroller->delete($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid order ID in URL']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>