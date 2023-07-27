<?php
require_once(__DIR__ . '/../utils/logger.php');
log_message('UPDATE: at the start');

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
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["productID"]) && isset($_POST["quantity"])) {
        $productID = $_POST["productID"];
        $quantity = $_POST["quantity"];

        require_once __DIR__.'/../utils/database.php';

        try {
            log_message('UPDATE: before getting connection');
            // Create a new PDO instance
            $pdo = getConnection();

            // Check if the quantity is numeric and non-negative
            if (!is_numeric($quantity) || $quantity < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid quantity. Quantity must be a non-negative number.']);
                exit;
            }

            // Check if the quantity is not blank
            if ($quantity === '') {
                echo json_encode(['status' => 'error', 'message' => 'Quantity cannot be blank.']);
                exit;
            }

            // If the quantity is updated to 0, delete the entry from the table
            if ($quantity == 0) {
                $stmt = $pdo->prepare("DELETE FROM ProductTable WHERE ProductID = :productID");
                $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
                exit;
            }

            // Prepare and execute the update query
            $stmt = $pdo->prepare("UPDATE ProductTable SET Quantity = :quantity WHERE ProductID = :productID");
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
            $stmt->execute();

            log_message('UPDATE: after execute');
            echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully']);
        } catch (PDOException $error) {
            log_message('UPDATE: inside catch PDOException: ' . $error->getMessage() . 'error');
            echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
        }
    } else {
        log_message('UPDATE: Invalid input data');
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
    }
}
?>
