<?php
// Include  authentication middleware
require_once '../authmiddleware.php';
require_once '../../config/database.php'; 
require '../../vendor/autoload.php';  // Include  PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get  username from the POST body
$data = json_decode(file_get_contents("php://input"), true);
$username = isset($data['username']) ? $data['username'] : null;

if (!$username) {
    http_response_code(400); // Bad request
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

    // SQL query to fetch attendees
    $sql = "SELECT 
                a.id AS attendee_id,
                e.name AS event_name,
                e.description AS event_description,
                u.username AS user_name,
                u.email AS user_email,
                a.registered_at
            FROM attendees a
            JOIN events e ON a.event_id = e.id
            JOIN users u ON a.user_id = u.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all the attendees
    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a new spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    
    $sheet->setCellValue('A1', 'Attendee ID');
    $sheet->setCellValue('B1', 'Event Name');
    $sheet->setCellValue('C1', 'Event Description');
    $sheet->setCellValue('D1', 'Username');
    $sheet->setCellValue('E1', 'User Email');
    $sheet->setCellValue('F1', 'Registered At');
   

    $row = 2;
    foreach ($attendees as $attendee) {
        $sheet->setCellValue('A' . $row, $attendee['attendee_id']);
        $sheet->setCellValue('B' . $row, $attendee['event_name']);
        $sheet->setCellValue('C' . $row, $attendee['event_description']);
        $sheet->setCellValue('D' . $row, $attendee['user_name']);
        $sheet->setCellValue('E' . $row, $attendee['user_email']);
        $sheet->setCellValue('F' . $row, $attendee['registered_at']);
        $row++;
    }

    // Write the spreadsheet to output
    $writer = new Xlsx($spreadsheet);
    $filename = "users_".time().".xlsx";
    $writer->save("../../output/".$filename);
    echo json_encode(["status" => "success", "message" => "Attendee report generated successfully.", "file" => $filename]);
    exit();

} catch (PDOException $e) {
    // Handle error and return a response
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "An error occurred while fetching the attendee report.", "error" => $e->getMessage()]);
    exit();
}
