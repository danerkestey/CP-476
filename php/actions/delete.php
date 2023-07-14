<?php
require_once '../utils/database.php';

// Capture POST data
$id = $_POST['SupplierId'];

// Prepare a SQL statement to delete a record from the Product table
$stmt = $pdo->prepare("DELETE FROM ProductTable WHERE SupplierId = :id");

// Bind the parameter and execute the statement
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Check if any rows were affected (i.e., the product was successfully deleted)
if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not delete product']);
}
?>
