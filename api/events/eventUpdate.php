<?php
// Include necessary files
require_once '../../config/database.php';
require_once '../authmiddleware.php';

// Authenticate the user
$payload = authenticate(); // If authentication fails, it exits
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

$eventId = filter_var($data['eventId'], FILTER_VALIDATE_INT);
$username = filter_var($data['username'], FILTER_SANITIZE_STRING);
$eventName = isset($data['eventName']) ? htmlspecialchars(strip_tags($data['eventName'])) : null;
$eventDescription = isset($data['eventDescription']) ? htmlspecialchars(strip_tags($data['eventDescription'])) : null;
$maxCapacity = isset($data['maxCapacity']) ? filter_var($data['maxCapacity'], FILTER_VALIDATE_INT) : null;

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

    // Check if the event belongs to the authenticated user
    if ($event['username'] !== $username) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "You are not authorized to update this event."]);
        exit();
    }

    // Prepare SQL query to update the event
    $sql = "UPDATE events SET name = :eventName, description = :eventDescription, max_capacity = :maxCapacity WHERE id = :eventId";
    $stmt = $pdo->prepare($sql);

    // Bind parameters to the placeholders in the query
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->bindParam(':eventName', $eventName, PDO::PARAM_STR);
    $stmt->bindParam(':eventDescription', $eventDescription, PDO::PARAM_STR);
    $stmt->bindParam(':maxCapacity', $maxCapacity, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Event updated successfully."]);
    } else {
        throw new Exception("Failed to update event.");
    }
} catch (PDOException $e) {
    // Log the actual error message for debugging
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error."]);
} catch (Exception $e) {
    // Log the exception message and provide it for debugging
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
