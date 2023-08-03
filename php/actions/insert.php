<?php
require_once(__DIR__ . '/../utils/logger.php');

ini_set('display_errors', 0);
error_reporting(0);

// Define a list of restricted words (SQL keywords or statements)
$restrictedWords = array('SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'TRUNCATE', 'ALTER', 'CREATE', 'TABLE', 'DATABASE');

// Function to check if the input contains any restricted word
function containsRestrictedWord($input) {
    global $restrictedWords;
    foreach ($restrictedWords as $word) {
        if (stripos($input, $word) !== false) {
            return true;
        }
    }
    return false;
}

// Catch all errors and exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['status' => 'error', 'message' => $errstr]);
    exit;
}, E_ALL);

set_exception_handler(function($exception) {
    echo json_encode(['status' => 'error', 'message' => $exception->getMessage()]);
    exit;
});

// Capture POST data
$table = isset($_POST['table']) ? $_POST['table'] : '';

require_once __DIR__.'/../utils/database.php';

// Valid table names
$validTables = ['Product', 'Supplier'];

if (!in_array($table, $validTables)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

// Check for restricted words in the input data
foreach ($_POST as $key => $value) {
    if (containsRestrictedWord($value)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input: Restricted word found']);
        exit;
    }
}

// Create a new PDO instance
$pdo = getConnection();

// Get table name
$tableName = $table;

// Determine the table to insert into
switch ($table) {
    case 'Product':
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
        $supplierFields = ['SupplierID', 'SupplierName', 'Address', 'Phone', 'Email'];

        $missingFields = array_diff($supplierFields, array_keys($_POST));
        if (!empty($missingFields)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: ' . implode(', ', $missingFields)]);
            exit;
        }

        $sql = "INSERT INTO SupplierTable (SupplierID, SupplierName, Address, Phone, Email) VALUES (:SupplierID, :SupplierName, :Address, :Phone, :Email)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':SupplierID', $_POST['SupplierID'], PDO::PARAM_INT);
        $stmt->bindParam(':SupplierName', $_POST['SupplierName'], PDO::PARAM_STR);
        $stmt->bindParam(':Address', $_POST['Address'], PDO::PARAM_STR);
        $stmt->bindParam(':Phone', $_POST['Phone'], PDO::PARAM_STR);
        $stmt->bindParam(':Email', $_POST['Email'], PDO::PARAM_STR);

        break;


    default: 
        echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
        exit;
}

try{
    // Execute the statement
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;

} catch(PDOException $e) {
    if ($e->getCode() == 23000) {
        if ($tableName == 'Product') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Please add a supplier with this Supplier ID before adding it to the Product table.'
            ]);
            exit;
            
        } else if ($tableName == 'Supplier') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Duplicate values entered, please enter unique values.'
            ]);
            exit;
        }

    } else {
        // If there is an error when executing the statement, throw an exception
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        echo error_log($e->getMessage());
        exit;
    }
    

} catch(Exception $e) {
    // If there is an error when executing the statement, throw an exception
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

?>
