<?php
require_once(__DIR__ . '/../utils/logger.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../utils/database.php';

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

// Valid table names
$validTables = ['Inventory', 'Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

// Create a new PDO instance
$pdo = getConnection();

// Determine the table to search
switch ($table) {
    case 'Product':
        $tableName = 'ProductTable';
        $results = refreshTable($pdo, $tableName);
        break;

    case 'Supplier':
        $tableName = 'SupplierTable';
        $results = refreshTable($pdo, $tableName);
        break;

    case 'Inventory':
        $tableName = 'InventoryTable';
        $results = refreshInventoryTable($pdo);
        break;

    default: // Default to invalid table
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

function refreshTable($pdo, $tableName) {
    
    // Prepare a SQL statement to get all items from the specified table
    $stmt = $pdo->prepare("SELECT * FROM $tableName");

    // Execute the statement
    $stmt->execute();

    // Fetch all items from the table
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}

function refreshInventoryTable($pdo) {
    // The SQL query to retrieve data for the Inventory table
    $sql = "SELECT p.ProductID, p.ProductName, p.Quantity, p.Price, p.Status, s.SupplierName
            FROM ProductTable p
            INNER JOIN SupplierTable s ON p.SupplierID = s.SupplierID";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all items from the inventory table
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if($results) {
    echo json_encode(['status' => 'success', 'results' => $results]);
    exit;
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'No items found']);
    exit;
}

?>
