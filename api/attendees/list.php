<?php
// Include  authentication middleware
require_once '../authmiddleware.php';
require_once '../../config/database.php';

// Get the username from the POST body
$data = json_decode(file_get_contents("php://input"), true);
$username = isset($data['username']) ? $data['username'] : null;

if (!$username) {
    http_response_code(400); 
    echo json_encode(["status" => "error", "message" => "Username is required."]);
    exit();
}

try {
    // Query to get the user's role from the database using the username
    $role_sql = "SELECT role FROM users WHERE username = :username";
    $role_stmt = $pdo->prepare($role_sql);
    $role_stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $role_stmt->execute();
    
    // Fetch the user's role
    $user_role = $role_stmt->fetchColumn();

    // Check if the user has the 'admin' role
    if ($user_role !== 'admin') {
        http_response_code(403); // Forbidden access
        echo json_encode(["status" => "error", "message" => "Unauthorized: You must be an admin to view the report."]);
        exit();
    }

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $records_per_page = 10; // Set the number of records per page

    // Calculate the offset for SQL query
    $start_from = ($page - 1) * $records_per_page;

    // Query to fetch the attendee report with event and user details
    $sql = "SELECT 
                a.id AS attendee_id,
                e.name AS event_name,
                e.description AS event_description,
                u.username AS user_name,
                u.email AS user_email,
                a.registered_at
            FROM attendees a
            JOIN events e ON a.event_id = e.id
            JOIN users u ON a.user_id = u.id
            ORDER BY a.registered_at DESC
            LIMIT :start_from, :records_per_page";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
    $stmt->bindValue(':records_per_page', $records_per_page, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all the attendees for the current page
    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to get the total number of attendees (for pagination)
    $total_sql = "SELECT COUNT(*) FROM attendees";
    $total_stmt = $pdo->query($total_sql);
    $total_records = $total_stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);

    // Prepare the response data
    echo json_encode([
        "status" => "success",
        "current_page" => $page,
        "total_pages" => $total_pages,
        "total_records" => $total_records,
        "records_per_page" => $records_per_page,
        "attendees" => $attendees
    ]);
} catch (PDOException $e) {
    
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "An error occurred while fetching the attendee report.", "error" => $e->getMessage()]);
    exit();
}
?>
