<?php
require_once __DIR__ . '/vendor/autoload.php';

use Config\EnvLoader;

// Load .env based on APP_ENV
EnvLoader::load(__DIR__);


// === CORS Headers for all responses ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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

    case '/api/installers':
        require __DIR__ . '/routes/installer.php';
        break;

    case (preg_match('/^\/api\/installers\/(\d+)$/', $request, $matches) ? true : false):
        $_GET['stateId'] = $matches[1];
        require __DIR__ . '/routes/installer.php';
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

    case '/api/queries':
        require __DIR__ . '/routes/queries.php';
        break;

    case (preg_match('/^\/api\/queries\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/queries.php';
        break;

    case '/api/quotes':
        require __DIR__ . '/routes/quote.php';
        break;

    case (preg_match('/^\/api\/quotes\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/quote.php';
        break;

    case '/api/contact-us':
        require __DIR__ . '/routes/contact-us.php';
        break;

    case (preg_match('/^\/api\/contact-us\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/contact-us.php';
        break;

    case '/api/states':
        require __DIR__ . '/routes/state.php';
        break;

    case (preg_match('/^\/api\/states\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/states.php';
        break;


    case '/api/orders':
        require __DIR__ . '/routes/order.php';
        break;

    case (preg_match('/^\/api\/orders\/(\d+)$/', $request, $matches) ? true : false):
        require __DIR__ . '/routes/orders.php';
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