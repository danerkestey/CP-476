<?php
require_once(__DIR__ . '/../utils/logger.php');
log_message('REFRESH: at the start');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../utils/database.php';

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

// Valid table names
$validTables = ['Inventory', 'Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    log_message('REFRESH: Invalid table: ' . $table . 'error');
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

log_message('REFRESH: before getting connection');
// Create a new PDO instance
$pdo = getConnection();
log_message('REFRESH: after getting connection');

log_message('REFRESH: before switch statement');
// Determine the table to search
switch ($table) {
    case 'Product':
        log_message('REFRESH: inside switch statement for Product');
        $tableName = 'ProductTable';
        $results = refreshTable($pdo, $tableName);
        break;
    case 'Supplier':
        log_message('REFRESH: inside switch statement for Supplier');
        $tableName = 'SupplierTable';
        $results = refreshTable($pdo, $tableName);
        break;
    case 'Inventory':
        log_message('REFRESH: inside switch statement for Inventory');
        $tableName = 'InventoryTable';
        $results = refreshInventoryTable($pdo);
        break;

    default: // Default to invalid table
        log_message('REFRESH: inside switch statement for Default');
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

function refreshTable($pdo, $tableName) {
    log_message('REFRESH: The SQL Query is: ' . "SELECT * FROM $tableName");
    
    // Prepare a SQL statement to get all items from the specified table
    $stmt = $pdo->prepare("SELECT * FROM $tableName");
    log_message('REFRESH: after prepare statement');

    log_message('REFRESH: before execute statement');
    // Execute the statement
    $stmt->execute();
    log_message('REFRESH: after execute statement');

    log_message('REFRESH: before fetchAll statement');
    // Fetch all items from the table
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    log_message('REFRESH: after fetchAll statement');

    return $results;
}

function refreshInventoryTable($pdo) {
    log_message('REFRESH: Refresh Inventory Table function');
    
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
    log_message('REFRESH: results found');
    echo json_encode(['status' => 'success', 'results' => $results]);
    exit;
} else {
    log_message('REFRESH: no results found');
    echo json_encode(['status' => 'error', 'message' => 'No items found']);
    exit;
}

?>
