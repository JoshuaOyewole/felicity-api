<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function decodeJWT($token)
{
    try {
        $secret = $_ENV['JWT_SECRET'];
        return (array) JWT::decode($token, new Key($secret, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}

function generateToken($admin)
{
    $payload = [
        'sub' => $admin['id'],
        'email' => $admin['email'],
        'role' => $admin['role'],
        'exp' => time() + 60 * 60 * 24 // 24 hours
    ];

    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}

function verifyToken($token)
{
    try {
        return JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}
