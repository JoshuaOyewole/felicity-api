<?php


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProjectShowcase.php';
require_once __DIR__ . '/../controllers/ProjectShowcaseController.php';

$database = new Database();
$db = $database->connect();

$projectController = new ProjectShowcaseController($db);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($requestMethod) {
    case 'GET':

        if (preg_match('/^\/api\/project_showcases\/state\/(\d+)$/', $requestPath, $matches)) {
            $projectController->getByState($matches[1]);
        } else if (preg_match('/^\/api\/project_showcases\/(\d+)$/', $requestPath, $matches)) {
            $projectController->getById($matches[1]);
        } else if (preg_match('/^\/api\/project_showcases$/', $requestPath)) {
            $projectController->getAll();
        } else if (preg_match('/\/api\/states-with-projects$/', $requestPath, $matches)) {
            $projectController->getStatesWithProjects();
        }else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'error' => 'Oops! The endpoint you’re looking for doesn’t exist.']);
        }
        break;

    case 'POST':
        if (preg_match('/^\/api\/project_showcases$/', $requestPath)) {
             requireSuperAdmin();
            $projectController->create();
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'error' => 'Endpoint not found']);
        }
        break;

    case 'PUT':
        if (preg_match('/^\/api\/project_showcases\/(\d+)$/', $requestPath, $matches)) {
             requireSuperAdmin();
            $projectController->update($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'error' => 'Endpoint not found']);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/api\/project_showcases\/(\d+)$/', $requestPath, $matches)) {
             requireSuperAdmin();
            $projectController->delete($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'error' => 'Endpoint not found']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 405, 'error' => 'Method not allowed']);
        break;
}
?>