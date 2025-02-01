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
if (!isset($data['event_id']) || !isset($data['username'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit();
}

// Extract and sanitize inputs
$event_id = filter_var($data['event_id'], FILTER_VALIDATE_INT);
$username = htmlspecialchars(strip_tags($data['username']));

if (!$event_id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid event ID."]);
    exit();
}

try {
    // Fetch user_id based on username
    $userQuery = "SELECT id FROM users WHERE username = :username";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit();
    }

    $user_id = $user['id'];

    // Check event max capacity
    $capacityQuery = "SELECT e.max_capacity, COUNT(a.id) AS register_count FROM events e LEFT JOIN attendees a ON e.id = a.event_id WHERE e.id = :event_id GROUP BY e.id";
    $capacityStmt = $pdo->prepare($capacityQuery);
    $capacityStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $capacityStmt->execute();
    $eventData = $capacityStmt->fetch(PDO::FETCH_ASSOC);

    if ($eventData && $eventData['register_count'] >= $eventData['max_capacity']) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Event registration is full."]);
        exit();
    }

    // Insert into event registrations
    $registerQuery = "INSERT INTO attendees (event_id, user_id, registered_at) VALUES (:event_id, :user_id, NOW())";
    $registerStmt = $pdo->prepare($registerQuery);
    $registerStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $registerStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($registerStmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "User registered for the event successfully."]);
    } else {
        throw new Exception("Failed to register user for the event.");
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
