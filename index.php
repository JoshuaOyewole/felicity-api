<?php
// === CORS Headers for all responses ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// === Respond to preflight (OPTIONS) requests ===
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === Basic router ===
$request = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

switch ($request) {
    case '/api/blogs':
        require __DIR__ . '/routes/blogs.php';
        break;

    case (preg_match('/^\/api\/blogs\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/blogs.php';
        break;

    case '/api/products':
        require __DIR__ . '/routes/products.php';
        break;

    case (preg_match('/^\/api\/products\/category\/(\d+)$/', $request, $matches) ? true : false):
        $_GET['categoryId'] = $matches[1];
        require __DIR__ . '/routes/products.php';
        break;

    case (preg_match('/^\/api\/products\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/products.php';
        break;

    case '/api/categories':
        require __DIR__ . '/routes/category.php';
        break;

    case (preg_match('/^\/api\/categories\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/categories.php';
        break;

    case '/api/testimonials':
        require __DIR__ . '/routes/testimonials.php';
        break;

    case (preg_match('/^\/api\/testimonials\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/testimonials.php';
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'status' => '404',
            'error' => "Oops! The endpoint you’re looking for doesn’t exist."
        ]);
        break;
}
?>
