<?php
// Include the authentication middleware
require_once '../authmiddleware.php';
require_once '../../config/database.php'; 

// Authenticate the request
$payload = authenticate(); // If the token is invalid, this will exit the script

// Get the page number from the query string (default to 1 if not set)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 10; // Set the number of records per page

// Get the sort order from frontend (default to DESC if not set)
$sort_order = isset($_GET['sortOrder']) && in_array(strtoupper($_GET['sortOrder']), ['ASC', 'DESC']) ? strtoupper($_GET['sortOrder']) : 'DESC';

// Get the sort field from frontend (default to name if not set)
$sort_by = isset($_GET['sortBy']) && in_array(strtolower($_GET['sortBy']), ['name', 'description', 'max_capacity', 'left_capacity']) ? strtolower($_GET['sortBy']) : 'name';

// Calculate the offset for SQL query
$start_from = ($page - 1) * $records_per_page;

try {
    // Query to fetch events with left capacity, ordered based on user input
    $sql = "SELECT 
                e.*, 
                (e.max_capacity - COALESCE(COUNT(a.id), 0)) AS left_capacity
            FROM events e
            LEFT JOIN attendees a ON e.id = a.event_id
            GROUP BY e.id
            ORDER BY e." . $sort_by . " " . $sort_order . " 
            LIMIT :start_from, :records_per_page";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
    $stmt->bindValue(':records_per_page', $records_per_page, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all the events for the current page
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to get the total number of records (for pagination)
    $total_sql = "SELECT COUNT(*) FROM events";
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
        "events" => $events
    ]);
} catch (PDOException $e) {
    // Handle error and return a response
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "An error occurred while fetching events.", "error" => $e->getMessage()]);
    exit();
}
?>
