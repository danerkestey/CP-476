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
    // Prepare a SQL statement to retrieve the user from the UserTable
    $stmt = $pdo->prepare("SELECT * FROM UserTable WHERE username = :username");

    // Bind the parameter and execute the statement
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user was found and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
