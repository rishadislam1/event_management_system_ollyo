<?php
require '../../config/database.php';
require '../../config/jwt_helper.php';



header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 minutes

define('JWT_EXPIRY', 3600); // 1 hour expiration time

try {
    session_start();
    
    $postData = json_decode(file_get_contents('php://input'), true);
   
    if (!isset($postData['email']) || !isset($postData['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    // validate email input
    $email = filter_var($postData['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    $password = htmlspecialchars($postData['password'], ENT_QUOTES, 'UTF-8');

    // Prevent brute force attacks with login attempt tracking
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE attempt_time < :expiry');
    $stmt->execute(['expiry' => time() - LOCKOUT_TIME]);

    // Check if IP is locked out
    $stmt = $pdo->prepare('SELECT COUNT(*) AS attempts FROM login_attempts WHERE ip_address = :ip');
    
 
    $stmt->execute(['ip' => $ipAddress]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if ($result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again later.']);
        exit;
    }

    // Fetch user data from users table
    $stmt = $pdo->prepare('SELECT id, email, password, role,username FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
     
        $stmt = $pdo->prepare('INSERT INTO login_attempts (ip_address, attempt_time) VALUES (:ip, :time)');
        $stmt->execute(['ip' => $ipAddress, 'time' => time()]);

        echo json_encode(['status' => "error", 'message' => 'Invalid email or password.']);
        exit;
    }

    // Verify the password
    if (!password_verify($password, $user['password'])) {
      
        $stmt = $pdo->prepare('INSERT INTO login_attempts (ip_address, attempt_time) VALUES (:ip, :time)');
        $stmt->execute(['ip' => $ipAddress, 'time' => time()]);

        echo json_encode(['status' => "error", 'message' => 'Invalid email or password.']);
        exit;
    }

    // Generate JWT token using the custom Token class
    $payload = [
        'sub' => $user['id'],     // Subject (user ID)
        'email' => $user['email'], // User email
        'role' => $user['role'],  // User role
        'username'=>$user['username'], //username
        'iat' => time(),          // Issued at time
        'exp' => time() + JWT_EXPIRY // Expiration time
    ];
 
    $jwt = Token::Sign($payload, $env['JWT_SECRET_KEY'], JWT_EXPIRY);
    $_SESSION['token'] = $jwt;  
    // Clear any login attempts after successful login
    $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE ip_address = :ip');
  
    $stmt->execute(['ip' => $ipAddress]);

    echo json_encode([
        'status' => true,
        'message' => 'Login successful.',
        'role' => $user['role'],
        'username'=>$user['username'],
        'token' => $jwt
    ]);
} catch (Exception $e) {
  
    error_log('Login Error: ' . $e->getMessage());

    echo json_encode(['status' => "error", 'message' => 'An error occurred. Please try again later.']);
}
