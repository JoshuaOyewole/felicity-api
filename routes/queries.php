<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/QueriesController.php';

$database = new Database();
$db = $database->connect();

$queryController = new QueriesController($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
         $matches = [];
       
        // Check if URL matches /api/queries/{id}
        if (preg_match('/^\/api\/queries\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $queryController->getOne($matches[1]);
        } else {
            $queryController->getAll();
        }
        break;

    case 'POST':
        $queryController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/queries\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
             requireSuperAdmin();
            $queryController->delete($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid query ID in URL']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>