<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/CategoriesController.php';

$database = new Database();
$db = $database->connect();

$categoriesController = new CategoriesController($db);


// Assuming the $categoriesController is already instantiated somewhere earlier in the code
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];
        // Check if the URL matches /api/products/{id}
        if (preg_match('/^\/api\/categories\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $categoriesController->getOne($matches[1]); // Fetch a single product
        } else {
            $categoriesController->getAll(); // Fetch all products
        }
        break;
    case 'POST':
        // Create a new category
        $categoriesController->create();
        break;

    case 'DELETE':
        // Check if the URL matches the pattern for blog ID
        $matches = [];
        if (preg_match('/^\/api\/categories\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            // Call the delete function with the blog ID
            $categoriesController->delete($matches[1]);
        } else {
            // Return 400 if the URL does not have a valid blog ID
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid blog ID in URL. Please ensure the ID is an integer.'
            ]);
        }
        break;

    case 'PUT':
        // Extract the blog ID from the URL (this should be like /api/categories/{id})
        $matches = [];
        if (preg_match('/^\/api\/categories\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $id = $matches[1];
            $categoriesController->update($id);  // Call the update method from the controller
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid category ID in URL'
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