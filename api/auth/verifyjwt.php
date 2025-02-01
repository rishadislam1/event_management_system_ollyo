<?php
require '../../config/database.php';
require '../../config/jwt_helper.php';
$env = parse_ini_file('../../.env');
$secretKey = $env['JWT_SECRET_KEY']; 

// Set the response header to JSON
header('Content-Type: application/json');

// Read the input token from the request body
$data = json_decode(file_get_contents('php://input'), true);
$token = isset($data['token']) ? $data['token'] : null;

// Check if token is provided
if (!$token) {
    echo json_encode([
        'status' => "error",
        'message' => 'Token is missing.'
    ]);
    exit;
}

try {
    $payload = Token::Verify($token, $secretKey);

   
    echo json_encode([
        'status' => "success",
        'message' => 'Token is valid.',
        'payload' => $payload
    ]);
} catch (Exception $e) {
   
    echo json_encode([
        'status' => "error",
        'message' => 'Token is invalid or expired.',
        'error' => $e->getMessage()
    ]);
}

exit;
