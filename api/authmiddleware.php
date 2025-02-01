<?php
// Include required files
require_once '../../config/database.php';
require_once '../../config/jwt_helper.php';

// Set response headers
header("Content-Type: application/json");

// Function to verify JWT token
function authenticate()
{
    // Get all headers
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? trim($headers['Authorization']) : null;

    // Extract Bearer token
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Unauthorized access. Token is missing."]);
        exit();
    }

    $token = trim($matches[1]); // Extract token part

    // Load secret key securely from .env
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Internal server error. Missing configuration."]);
        exit();
    }

    $env = parse_ini_file($envPath);
    if (!$env || !isset($env['JWT_SECRET_KEY'])) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Internal server error. Invalid configuration."]);
        exit();
    }

    $secretKey = $env['JWT_SECRET_KEY'];

    // Verify the token
    $payload = Token::Verify($token, $secretKey);
    
    if (!$payload) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Unauthorized access. Invalid or expired token."]);
        exit();
    }

    return true; // Token is valid, return payload
}
