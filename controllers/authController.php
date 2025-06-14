<?php
use Models\Admin;

require_once __DIR__ . '/../models/admin.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/jwt.php';

function loginSuperAdmin()
{
    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    $admin = Admin::findByEmail($email);

    if (!$admin || !password_verify($password, $admin['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        return;
    }

    if ($admin['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied. Not a Super Admin']);
        return;
    }

    $token = generateToken($admin);

    setcookie("token", $token, [
        'expires' => time() + 60 * 60 * 24,
        'httponly' => true,
        'path' => '/',
        'samesite' => 'Lax'
        // 'secure' => true // Only enable in HTTPS
    ]);

    http_response_code(200);
    echo json_encode([
        'message' => 'Login successful',
        'status' => 200,
        'admin' => [
            'id' => $admin['id'],
            'email' => $admin['email'],
            'role' => $admin['role']
        ]
    ]);
}