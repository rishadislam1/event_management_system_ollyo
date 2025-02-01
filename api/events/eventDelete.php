<?php

require_once '../../config/database.php';
require_once '../authmiddleware.php';

// Authenticate the user
$payload = authenticate(); 
// Set response headers
header("Content-Type: application/json");

// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (!isset($data['eventId'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required field: eventId."]);
    exit();
}

// Extract and sanitize inputs
$eventId = filter_var($data['eventId'], FILTER_VALIDATE_INT);
$username = filter_var($data['username'], FILTER_SANITIZE_STRING);

if (!$eventId || $eventId <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid event ID."]);
    exit();
}

try {
    // Check if the event exists
    $sql = "SELECT * FROM events WHERE id = :eventId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Event not found."]);
        exit();
    }
    
   
    if ($event['username'] !== $username) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "You are not authorized to delete this event."]);
        exit();
    }

    // Prepare SQL query to delete the event
    $sql = "DELETE FROM events WHERE id = :eventId";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters to the placeholders in the query
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Event deleted successfully."]);
    } else {
        throw new Exception("Failed to delete event.");
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
?>
