<?php
require_once '../utils/database.php';

// Capture POST data
$searchTerm = $_POST['searchTerm'];

// Prepare a SQL statement to search for products in the ProductTable
$stmt = $pdo->prepare("SELECT * FROM ProductTable WHERE ProductName LIKE :searchTerm");

// Bind the parameters and execute the statement
$stmt->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
$stmt->execute();

// Fetch all products that match the search term
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($results) {
    echo json_encode(['status' => 'success', 'results' => $results]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No products found']);
}
?>
