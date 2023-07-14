<?php
require_once 'database.php';

try {
    // Clear out the tables -> just for demo purposes to avoid duplicate entries when re-ran multiple times
    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Clear out the tables
    $pdo->exec("TRUNCATE TABLE ProductTable"); // Truncate ProductTable first
    $pdo->exec("TRUNCATE TABLE SupplierTable"); // Then truncate SupplierTable

    // Enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Populate Supplier table
    $file = fopen(__DIR__ . '/SupplierFile.csv', 'r');
    if (!$file) {
        throw new Exception("Unable to open SupplierFile.csv");
    }

    $stmt = $pdo->prepare("INSERT INTO SupplierTable (SupplierID, SupplierName, Address, Phone, Email) VALUES (:SupplierID, :SupplierName, :Address, :Phone, :Email)");

    while (($row = fgetcsv($file)) !== false) {
        // Bind the parameters
        $stmt->bindValue(':SupplierID', $row[0], PDO::PARAM_INT);
        $stmt->bindValue(':SupplierName', $row[1], PDO::PARAM_STR);
        $stmt->bindValue(':Address', $row[2], PDO::PARAM_STR);
        $stmt->bindValue(':Phone', $row[3], PDO::PARAM_STR);
        $stmt->bindValue(':Email', $row[4], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();
    }
    fclose($file);

    // Populate Product table
    $file = fopen(__DIR__ . '/ProductFile.csv', 'r');
    if (!$file) {
        throw new Exception("Unable to open ProductFile.csv");
    }

    $stmt = $pdo->prepare("INSERT INTO ProductTable (ProductID, ProductName, Description, Price, Quantity, Status, SupplierID) VALUES (:ProductID, :ProductName, :Description, :Price, :Quantity, :Status, :SupplierID)");

    while (($row = fgetcsv($file)) !== false) {
        // Bind the parameters
        $stmt->bindValue(':ProductID', $row[0], PDO::PARAM_INT);
        $stmt->bindValue(':ProductName', $row[1], PDO::PARAM_STR);
        $stmt->bindValue(':Description', $row[2], PDO::PARAM_STR);
        
        // Remove the dollar sign from the price
        $price = str_replace('$', '', $row[3]);
        $stmt->bindValue(':Price', $price, PDO::PARAM_STR);
        
        $stmt->bindValue(':Quantity', $row[4], PDO::PARAM_INT);
        $stmt->bindValue(':Status', $row[5], PDO::PARAM_STR);
        $stmt->bindValue(':SupplierID', $row[6], PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();
    }
    fclose($file);

    echo "Tables populated successfully\n";

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>
