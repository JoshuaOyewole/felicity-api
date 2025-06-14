<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/QuoteController.php';

$database = new Database();
$db = $database->connect();

$quoteController = new QuoteController($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
         $matches = [];
       
        // Check if URL matches /api/products/{id}
        if (preg_match('/^\/api\/quotes\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $quoteController->getOne($matches[1]);
        } else {
            $quoteController->getAll();
        }
        break;

    case 'POST':
        $quoteController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/quotes\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
             requireSuperAdmin();
            $quoteController->delete($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid quote ID in URL']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>