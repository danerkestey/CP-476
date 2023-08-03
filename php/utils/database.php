<?php
require_once(__DIR__ . '/../utils/logger.php');
// Start the session
session_start();

function getConnection($username = '', $password = '') {
    // Set default values for the database connection
    $host = 'localhost';
    $db = 'CP476_Database';
    $charset = 'utf8mb4';

    // If username and password are set in the session, use them to connect to the database
    if (isset($_SESSION['username'], $_SESSION['password'])) {
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
    }

    // Data Source Name (DSN) contains the information required to connect to the database
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    // Options for the PDO object
    $opt = [
        // Throw exceptions when a database error occurs
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Set the default fetch mode to fetch associated arrays
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Disable emulated prepared statements
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, $username, $password, $opt);
    } catch(PDOException $e) {
        // If there is an error when creating the PDO object, throw an exception
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    // Return the PDO object
    return $pdo;
}
