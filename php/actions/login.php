<?php

ini_set('display_errors', 1); // REMOVE AFTER DEBUGGING
ini_set('display_startup_errors', 1); // REMOVE AFTER DEBUGGING
error_reporting(E_ALL); // REMOVE AFTER DEBUGGING

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

    // Start a new session if one isn't already active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // If the connection is successful, store the username and password in the session
    $_SESSION['username'] = $username; // Store username in session
    $_SESSION['password'] = $password; // Store password in session
    echo json_encode(['status' => 'success']);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
