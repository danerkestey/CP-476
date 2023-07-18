<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../utils/database.php';

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

// Valid table names
$validTables = ['Inventory', 'Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    die('Invalid table');
}

// Create a new PDO instance
$pdo = getConnection();

// Determine the table to search
switch ($table) {
    case 'Product':
        $tableName = 'ProductTable';
        break;
    case 'Supplier':
        $tableName = 'SupplierTable';
        break;
    default: // Default to inventory table
        $tableName = 'InventoryTable';
        break;
}

// Prepare a SQL statement to get all items from the specified table
$stmt = $pdo->prepare("SELECT * FROM $tableName");

// Execute the statement
$stmt->execute();

// Fetch all items from the table
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($results) {
    echo json_encode(['status' => 'success', 'results' => $results]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No items found']);
}
?>
