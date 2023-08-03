<?php
require_once(__DIR__ . '/../utils/logger.php');

ini_set('display_errors', 0);
error_reporting(0);

// Catch all errors and exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['status' => 'error', 'message' => $errstr]);
    exit;
}, E_ALL);

set_exception_handler(function($exception) {
    echo json_encode(['status' => 'error', 'message' => $exception->getMessage()]);
    exit;
});

require_once __DIR__.'/../utils/database.php';

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';
$id = isset($_POST['id']) ? $_POST['id'] : '';

// Valid table names
$validTables = ['Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
    exit;
}

// Create a new PDO instance
$pdo = getConnection();

// Determine the table to delete from
$tableIdField = "";
switch ($table) {
    case 'Product':
        $tableIdField = 'ProductID';
        break;
    case 'Supplier':
        $tableIdField = 'SupplierID';
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

$sql = "DELETE FROM {$table}Table WHERE {$tableIdField} = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

try {
    // Execute the statement
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;

} catch (PDOException $e) {

    if ($e->errorInfo[1] == 1451) {
        echo json_encode([
            'status' => 'error',
            'message' => 'There are products that have this Supplier ID. Please delete entries with this Supplier ID in Product Table first.'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

?>
