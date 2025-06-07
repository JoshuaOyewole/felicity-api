<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/StateController.php';

$database = new Database();
$db = $database->connect();

$stateController = new StateController($db);


// Assuming the $stateController is already instantiated somewhere earlier in the code
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];
        // Check if the URL matches /api/states/{id}
        if (preg_match('/^\/api\/states\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $stateController->getOne($matches[1]); // Fetch a single state
        } else {
            $stateController->getAll(); // Fetch all states
        }
        break;
    case 'POST':
        // Create a new state
        $stateController->create();
        break;

    case 'DELETE':
        // Check if the URL matches the pattern for state ID
        $matches = [];
        if (preg_match('/^\/api\/states\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            // Call the delete function with the state ID
            $stateController->delete($matches[1]);
        } else {
            // Return 400 if the URL does not have a valid state ID
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid state ID in URL. Please ensure the ID is an integer.'
            ]);
        }
        break;

    case 'PUT':
        // Extract the state ID from the URL (this should be like /api/state/{id})
        $matches = [];
        if (preg_match('/^\/api\/states\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $id = $matches[1];
            $stateController->update($id);  // Call the update method from the controller
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid state ID in URL'
            ]);
        }
        break;
    default:
        // If method is not GET, POST, or DELETE
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            'status' => '405',
            'error' => 'Method not allowed. Please use GET, POST, or DELETE.'
        ]);
        break;
}

?>