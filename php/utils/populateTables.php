<?php
require_once 'database.php';  // Include the database connection file

try {
    // Disable foreign key checks to allow for clearing out the tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Clear out the tables to avoid duplicate entries when re-run multiple times
    $pdo->exec("TRUNCATE TABLE ProductTable"); // Truncate ProductTable first
    $pdo->exec("TRUNCATE TABLE SupplierTable"); // Then truncate SupplierTable

    // Enable foreign key checks after clearing out the tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Begin populating Supplier table
    $file = fopen(__DIR__ . '/SupplierFile.csv', 'r');  // Open the CSV file with supplier data
    if (!$file) {
        throw new Exception("Unable to open SupplierFile.csv");
    }

    // Prepare the SQL insert statement for the SupplierTable
    $stmt = $pdo->prepare("INSERT INTO SupplierTable (SupplierID, SupplierName, Address, Phone, Email) VALUES (:SupplierID, :SupplierName, :Address, :Phone, :Email)");

    while (($row = fgetcsv($file)) !== false) {  // Read data from the CSV file line by line
        // Bind the parameters for the SQL insert statement
        $stmt->bindValue(':SupplierID', $row[0], PDO::PARAM_INT);
        $stmt->bindValue(':SupplierName', $row[1], PDO::PARAM_STR);
        $stmt->bindValue(':Address', $row[2], PDO::PARAM_STR);
        $stmt->bindValue(':Phone', $row[3], PDO::PARAM_STR);
        $stmt->bindValue(':Email', $row[4], PDO::PARAM_STR);

        // Execute the insert statement
        $stmt->execute();
    }
    fclose($file);  // Close the CSV file

    // Begin populating Product table
    $file = fopen(__DIR__ . '/ProductFile.csv', 'r');  // Open the CSV file with product data
    if (!$file) {
        throw new Exception("Unable to open ProductFile.csv");
    }

    // Prepare the SQL insert statement for the ProductTable
    $stmt = $pdo->prepare("INSERT INTO ProductTable (ProductID, ProductName, Description, Price, Quantity, Status, SupplierID) VALUES (:ProductID, :ProductName, :Description, :Price, :Quantity, :Status, :SupplierID)");

    while (($row = fgetcsv($file)) !== false) {  // Read data from the CSV file line by line
        // Bind the parameters for the SQL insert statement
        $stmt->bindValue(':ProductID', $row[0], PDO::PARAM_INT);
        $stmt->bindValue(':ProductName', $row[1], PDO::PARAM_STR);
        $stmt->bindValue(':Description', $row[2], PDO::PARAM_STR);
        
        // Remove the dollar sign from the price
        $price = str_replace('$', '', $row[3]);
        $stmt->bindValue(':Price', $price, PDO::PARAM_STR);
        
        $stmt->bindValue(':Quantity', $row[4], PDO::PARAM_INT);
        $stmt->bindValue(':Status', $row[5], PDO::PARAM_STR);
        $stmt->bindValue(':SupplierID', $row[6], PDO::PARAM_INT);

        // Execute the insert statement
        $stmt->execute();
    }
    fclose($file);  // Close the CSV file

    // Begin populating Inventory table
    // SQL insert statement that populates InventoryTable based on data in ProductTable and SupplierTable
    $sql = "INSERT INTO InventoryTable (ProductID, ProductName, Quantity, Price, Status, SupplierName) 
    SELECT p.ProductID, p.ProductName, p.Quantity, p.Price, p.Status, s.SupplierName
    FROM ProductTable p
    JOIN SupplierTable s ON p.SupplierID = s.SupplierID";

    // Execute the insert statement
    $pdo->exec($sql);
    echo "Inventory table populated successfully\n";

    echo "Tables populated successfully\n";

} catch (Exception $e) {
    // In case of any errors, output the error message
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>
