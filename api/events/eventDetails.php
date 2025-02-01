<?php
// Include the authentication middleware
require_once '../authmiddleware.php';

// Authenticate the request
$payload = authenticate(); 

// Get the event ID from the query string
if (!isset($_GET['eventId']) || !filter_var($_GET['eventId'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or missing eventId."]);
    exit();
}

$eventId = (int) $_GET['eventId'];

try {
    // Prepare and execute the query to get event details based on eventId
    $sql = "SELECT name,description,max_capacity FROM events WHERE id = :eventId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the event details
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        // If event is found, return the event details
        echo json_encode([
            "status" => "success",
            "data" => $event
        ]);
    } else {
        // If event is not found
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Event not found."]);
    }

} catch (PDOException $e) {
    // Handle error and return a response
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "An error occurred while fetching the event details."]);
    exit();
}
?>
