<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/StateController.php';

$database = new Database();
$db = $database->connect();

$stateController = new StateController($db);

// Get only the path without query string
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];

        // Match /api/states/{id}
        if (preg_match('/^\/api\/states\/(\d+)$/', $requestUri, $matches)) {
            $stateController->getOne($matches[1]); // Fetch a single state
        }
        // Match /api/states-with-projects
        else if ($requestUri === '/api/states-with-projects') {
            $stateController->getStatesWithProjects();
        }
        else if ($requestUri === '/api/states-with-installers') {
            $stateController->getStatesWithInstallers();
        }
        // Match /api/states
        else if ($requestUri === '/api/states') {
            $stateController->getAll();
        }
        // If route doesn't match any of the above
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Endpoint not found'
            ]);
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
             requireSuperAdmin();
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
             requireSuperAdmin();
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