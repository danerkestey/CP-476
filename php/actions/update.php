<?php
require_once '../utils/database.php';

// Capture POST data
$productId = $_POST['productId'];
$newQuantity = $_POST['newQuantity'];

// Prepare a SQL statement to update the quantity of a product in the ProductTable
$stmt = $pdo->prepare("UPDATE ProductTable SET Quantity = :newQuantity WHERE ProductID = :productId");

// Bind the parameters and execute the statement
$stmt->bindParam(':newQuantity', $newQuantity, PDO::PARAM_INT);
$stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
$stmt->execute();

// Check if the update was successful
if($stmt->rowCount()) {
    echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
}
?>
