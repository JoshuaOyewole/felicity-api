<?php
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if ($uri === '/api/login' && $method === 'POST') {
    require_once __DIR__ . '/../controllers/AuthController.php';
    loginSuperAdmin();
    exit;
}
