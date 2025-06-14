<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/TestimonialController.php';

$database = new Database();
$db = $database->connect();
$testimonialController = new TestimonialController($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $testimonialController->getAll();
        break;

    case 'POST':
        $testimonialController->create();
        break;

    case 'DELETE':
        $matches = [];
        if (preg_match('/^\/api\/testimonials\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
             requireSuperAdmin();
            $testimonialController->delete($matches[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid testimonial ID in URL']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>