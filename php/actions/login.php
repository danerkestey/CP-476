<?php
require_once '../utils/database.php';

header('Content-Type: application/json');

if (!isset($_POST['username'], $_POST['password']) || trim($_POST['username']) === '' || trim($_POST['password']) === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing username or password']);
    exit();
}

// Capture POST data
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // Initialize a PDO connection using the POSTed MySQL credentials
    $pdo = getConnection($username, $password);

    // If the connection is successful, start the session and store username and password in it
    session_start();
    $_SESSION['username'] = $username; // Store username in session
    $_SESSION['password'] = $password; // Store password in session
    echo json_encode(['status' => 'success']);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
