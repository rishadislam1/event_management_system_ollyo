<?php

require_once '../../config/database.php';
require_once '../authmiddleware.php';

// Authenticate the user
$payload = authenticate(); 

header("Content-Type: application/json");

// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (!isset($data['eventName']) || !isset($data['eventDescription']) || !isset($data['maxCapacity'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit();
}

$eventName = htmlspecialchars(strip_tags($data['eventName']));
$eventDescription = htmlspecialchars(strip_tags($data['eventDescription']));
$maxCapacity = filter_var($data['maxCapacity'], FILTER_VALIDATE_INT);
$username = filter_var($data['username'], FILTER_SANITIZE_STRING);

if (!$maxCapacity || $maxCapacity <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid max capacity."]);
    exit();
}

try {
    // Prepare SQL query to prevent SQL injection
    $sql = "INSERT INTO events (name, description, max_capacity, username) VALUES (:eventName, :eventDescription, :maxCapacity, :username)";
    
    $stmt = $pdo->prepare($sql);

    // Bind parameters to the placeholders in the qu ery
    $stmt->bindParam(':eventName', $eventName, PDO::PARAM_STR);
    $stmt->bindParam(':eventDescription', $eventDescription, PDO::PARAM_STR);
    $stmt->bindParam(':maxCapacity', $maxCapacity, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Event created successfully."]);
    } else {
        throw new Exception("Failed to create event.");
    }
} catch (PDOException $e) {
    
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error."]);
   
} catch (Exception $e) {
    
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
