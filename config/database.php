<?php
// List of allowed origins for CORS
$allowed_origins = [
    'http://localhost:5173', // Ensure no trailing slash
];

// Get the Origin header from the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// if (in_array($origin, $allowed_origins)) {
//     header("Access-Control-Allow-Origin: $origin");
//     header("Access-Control-Allow-Credentials: true"); // Required if using cookies or authentication
//     header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
//     header("Access-Control-Allow-Headers: Content-Type, Authorization");
// }
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$username = $env['DB_USER'];
$password = $env['DB_PASS'];

try {
    // Establish a secure PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set PDO attributes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);

    // Enforce MySQL strict mode
    $pdo->exec("SET SESSION sql_mode='STRICT_ALL_TABLES'");
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    // Log the error securely
    error_log($e->getMessage(), 3, __DIR__ . '/../logs/error.log');

    // Return a JSON response instead of `die()`
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => "A technical issue occurred. Please try again later."]);
    exit();
}
