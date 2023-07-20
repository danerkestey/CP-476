<?php
require_once(__DIR__ . '/../utils/logger.php');
log_message('INSERT: at the start');

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

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

require_once __DIR__.'/../utils/database.php';

// Valid table names
$validTables = ['Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    log_message('Invalid table: ' . $table . 'error');
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

log_message('INSERT: before getting connection');
// Create a new PDO instance
$pdo = getConnection();
log_message('INSERT: after getting connection');

log_message('INSERT: before switch statement');
// Determine the table to insert into
switch ($table) {
    case 'Product':
        log_message('INSERT: inside switch statement for Product');
        $productFields = ['ProductID', 'ProductName', 'Description', 'Price', 'Quantity', 'Status', 'SupplierID'];
        $missingFields = array_diff($productFields, array_keys($_POST));
        if (!empty($missingFields)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: ' . implode(', ', $missingFields)]);
            exit;
        }

        $sql = "INSERT INTO ProductTable (ProductID, ProductName, Description, Price, Quantity, Status, SupplierID) VALUES (:ProductID, :ProductName, :Description, :Price, :Quantity, :Status, :SupplierID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ProductID', $_POST['ProductID'], PDO::PARAM_INT);
        $stmt->bindParam(':ProductName', $_POST['ProductName'], PDO::PARAM_STR);
        $stmt->bindParam(':Description', $_POST['Description'], PDO::PARAM_STR);

        $price = $_POST['Price'];
        if (filter_var($price, FILTER_VALIDATE_FLOAT) === false) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid price']);
            exit;
        }

        $stmt->bindParam(':Price', $_POST['Price'], PDO::PARAM_STR);
        $stmt->bindParam(':Quantity', $_POST['Quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':Status', $_POST['Status'], PDO::PARAM_STR);
        $stmt->bindParam(':SupplierID', $_POST['SupplierID'], PDO::PARAM_INT);
        break;


    case 'Supplier':
        log_message('');
        log_message('INSERT: inside switch statement for Supplier');
        $supplierFields = ['SupplierID', 'SupplierName', 'Address', 'Phone', 'Email'];
        log_message('INSERT SUPPLIER CASE: A');
        $missingFields = array_diff($supplierFields, array_keys($_POST));
        log_message('INSERT SUPPLIER CASE: B');
        if (!empty($missingFields)) {
            log_message('INSERT SUPPLIER CASE: MISSING FIELDS');
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: ' . implode(', ', $missingFields)]);
            exit;
        }
        log_message('INSERT SUPPLIER CASE: C');
        $sql = "INSERT INTO SupplierTable (SupplierID, SupplierName, Address, Phone, Email) VALUES (:SupplierID, :SupplierName, :Address, :Phone, :Email)";
        log_message('INSERT SUPPLIER CASE: D');
        log_message("INSERT: sql statement is: " . "$sql");
        $stmt = $pdo->prepare($sql);
        log_message('INSERT SUPPLIER CASE: E');
        $stmt->bindParam(':SupplierID', $_POST['SupplierID'], PDO::PARAM_INT);
        log_message('INSERT SUPPLIER CASE: F');
        $stmt->bindParam(':SupplierName', $_POST['SupplierName'], PDO::PARAM_STR);
        log_message('INSERT SUPPLIER CASE: G');
        $stmt->bindParam(':Address', $_POST['Address'], PDO::PARAM_STR);
        log_message('INSERT SUPPLIER CASE: H');
        $stmt->bindParam(':Phone', $_POST['Phone'], PDO::PARAM_STR);
        log_message('INSERT SUPPLIER CASE: I');
        $stmt->bindParam(':Email', $_POST['Email'], PDO::PARAM_STR);
        log_message('INSERT SUPPLIER CASE: J');
        break;


    default: 
        log_message('INSERT: inside switch statement for default');
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

try{
    log_message('INSERT: inside try, before execute');
    // Execute the statement
    $stmt->execute();
    log_message('INSERT: inside try, after execute');
    echo json_encode(['status' => 'success']);
    exit;

} catch(PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please add a supplier with this Supplier ID before adding it to the Product table.'
        ]);
        exit;

    } else {
        log_message('INSERT: inside catch, PDOException: ' . $e->getMessage() . 'error');
        // If there is an error when executing the statement, throw an exception
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        echo error_log($e->getMessage());
        exit;
    }
    

} catch(Exception $e) {
    log_message('INSERT: inside catch, Exception: ' . $e->getMessage() . 'error');
    // If there is an error when executing the statement, throw an exception
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

?>