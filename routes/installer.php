<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/InstallersController.php';

$database = new Database();
$db = $database->connect();

$installerController = new InstallersController($db);

// Assuming the $installerController is already instantiated somewhere earlier in the code
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];
        // Check if URL matches /api/installers/{stateId}
        if (preg_match('/^\/api\/installers\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $installerController->getByState($matches[1]);
        }
        // Check if URL matches /api/installers/{id}
        else if (preg_match('/^\/api\/installers\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $installerController->getOne($matches[1]);
        } else {
            $installerController->getAll();
        }
        break;
       
    case 'POST':
        $installerController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/installers\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $installerController->delete($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid installer ID in URL. Please ensure the ID is an integer.'
            ]);
        }
        break;

    case 'PUT':
        $matches = [];
        if (preg_match('/^\/api\/installers\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $id = $matches[1];
            $installerController->update($id);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid installer ID in URL'
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