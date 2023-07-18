<?php
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Catch all errors and exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $errstr]);
    exit;
}, E_ALL);

set_exception_handler(function($exception) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $exception->getMessage()]);
    exit;
});

require_once __DIR__.'/../utils/database.php';
echo json_encode(['status' => 'debug', 'message' => 'after require_once']);

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

// Valid table names
$validTables = ['Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    die('Invalid table');
}

// Create a new PDO instance
$pdo = getConnection();

// Determine the table to insert into
switch ($table) {
    case 'Product':
        $sql = "INSERT INTO Product (ProductID, ProductName, Description, Price, Quantity, Status, SupplierID) VALUES (:ProductID, :ProductName, :Description, :Price, :Quantity, :Status, :SupplierID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ProductID', $_POST['ProductID']);
        $stmt->bindParam(':ProductName', $_POST['ProductName']);
        $stmt->bindParam(':Description', $_POST['Description']);
        $stmt->bindParam(':Price', $_POST['Price']);
        $stmt->bindParam(':Quantity', $_POST['Quantity']);
        $stmt->bindParam(':Status', $_POST['Status']);
        $stmt->bindParam(':SupplierID', $_POST['SupplierID']);
        break;
    case 'Supplier':
        $sql = "INSERT INTO Supplier (SupplierID, SupplierName, Address, Phone, Email) VALUES (:SupplierID, :SupplierName, :Address, :Phone, :Email)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':SupplierID', $_POST['SupplierID']);
        $stmt->bindParam(':SupplierName', $_POST['SupplierName']);
        $stmt->bindParam(':Address', $_POST['Address']);
        $stmt->bindParam(':Phone', $_POST['Phone']);
        $stmt->bindParam(':Email', $_POST['Email']);
        break;
    default: 
        die('Invalid table');
}

try{
    // Execute the statement
    $stmt->execute();
    ob_end_clean();
    echo json_encode(['status' => 'success']);

} catch(PDOException $e) {
    // If there is an error when executing the statement, throw an exception
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>