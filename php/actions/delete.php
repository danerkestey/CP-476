<?php
require_once(__DIR__ . '/../utils/logger.php');
log_message('DELETE: at the start');

ini_set('display_errors', 0);
error_reporting(0);

// Catch all errors and exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    log_message('error in set_error_handler: ' . $errstr . ' in ' . $errfile . ' on line ' . $errline . 'error');
    echo json_encode(['status' => 'error', 'message' => $errstr]);
    exit;
}, E_ALL);

set_exception_handler(function($exception) {
    log_message('error in set_exception_handler: ' . $exception->getMessage() . 'error');
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
    log_message('Invalid table: ' . $table . 'error');
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

if (empty($id)) {
    log_message('DELETE: No ID provided');
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
    exit;
}

log_message('DELETE: before getting connection');
// Create a new PDO instance
$pdo = getConnection();
log_message('DELETE: after getting connection');

// Determine the table to delete from
$tableIdField = "";
switch ($table) {
    case 'Product':
        log_message('DELETE: inside switch statement for Product');
        $tableIdField = 'ProductID';
        break;
    case 'Supplier':
        log_message('DELETE: inside switch statement for Supplier');
        $tableIdField = 'SupplierID';
        break;
    default:
        log_message('DELETE: inside switch statement for default');
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

$sql = "DELETE FROM {$table}Table WHERE {$tableIdField} = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

try {
    // Execute the statement
    $stmt->execute();
    log_message('DELETE: inside try, after execute');
    echo json_encode(['status' => 'success']);
    exit;

} catch (PDOException $e) {
    log_message('DELETE: inside catch, PDOException: ' . $e->getMessage() . 'error');

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
    log_message('DELETE: inside catch, Exception: ' . $e->getMessage() . 'error');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

?>
