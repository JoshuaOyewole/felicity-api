<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/BlogController.php';

$database = new Database();
$db = $database->connect();

$blogController = new BlogController($db);


// Assuming the $blogController is already instantiated somewhere earlier in the code
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $matches = [];
        // Check if the URL matches /api/products/{id}
        if (preg_match('/^\/api\/blogs\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $blogController->getOne($matches[1]); // Fetch a single product
        } else {
            $blogController->getAll(); // Fetch all products
        }
        break;
    case 'POST':
        // Create a new blog
        requireSuperAdmin();
        $blogController->create();
        break;

    case 'DELETE':
        // Check if the URL matches the pattern for blog ID
        $matches = [];
        if (preg_match('/^\/api\/blogs\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            // Call the delete function with the blog ID
            requireSuperAdmin();
            $blogController->delete($matches[1]);
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
        // Extract the blog ID from the URL (this should be like /api/blogs/{id})
        $matches = [];
        if (preg_match('/^\/api\/blogs\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
            $id = $matches[1];
            requireSuperAdmin();
            $blogController->update($id);  // Call the update method from the controller
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => '400',
                'error' => 'Invalid blog ID in URL'
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