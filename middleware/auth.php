<?php
require_once __DIR__ . '/../utils/jwt.php'; // Wherever your JWT helper lives

function requireSuperAdmin()
{
    if (!isset($_COOKIE['token'])) {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized: No token found',
            'status' => 401

        ]);
        exit;
    }

    $token = $_COOKIE['token'];
    $payload = decodeJWT($token); // ✅ You must have this helper already

    if (!$payload || ($payload['role'] ?? null) !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'error' => 'Forbidden! Super admin only',
            'status' => 403
        ]);
        exit;
    }
}
