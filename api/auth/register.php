<?php
include '../../config/database.php';

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $inputData = json_decode(file_get_contents("php://input"), true);

    // Check if data exists
    if (!$inputData) {
        echo json_encode(['status' => 'error', 'message' => 'No data received']);
        exit();
    }

    // Validation
    $username = isset($inputData['username']) ? trim($inputData['username']) : null;
    $email = isset($inputData['email']) ? trim($inputData['email']) : null;
    $password_raw = isset($inputData['password']) ? trim($inputData['password']) : null;

  
    if (!$username || strlen($username) < 3 || strlen($username) > 50) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username. Must be between 3 and 50 characters.']);
        exit();
    }

    // Validate email format
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit();
    }

    // Validate password
    if (!$password_raw || !preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_])[A-Za-z\d\W_]{6,}$/", $password_raw)) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters and contain 1 uppercase letter, 1 lowercase letter, and 1 special character.']);
        exit();
    }
    

    // Hash the password securely
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        // Use prepared statements to prevent SQL injection
        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            if ($existingUser['username'] === $username) {
                echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
            } elseif ($existingUser['email'] === $email) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            }
        } else {
           
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role'=>'user'
            ]);
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        }
    } catch (PDOException $e) {
        
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your request']);
    }
}
?>
