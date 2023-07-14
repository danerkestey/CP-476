<?php
// Include the database connection file
require_once 'database.php';

try {
    // Check if the Supplier table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'SupplierTable'");
    $stmt->execute();

    // If the Supplier table does not exist, create it
    if($stmt->rowCount() == 0) {
        // SQL statement to create Supplier table
        $sql = "CREATE TABLE SupplierTable (
            SupplierID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            SupplierName VARCHAR(255) NOT NULL,
            Address VARCHAR(255) NOT NULL,
            Phone VARCHAR(15) NOT NULL,
            Email VARCHAR(255) NOT NULL UNIQUE
        )";

        // Execute the statement
        $pdo->exec($sql);
        echo "Supplier table created successfully\n";
    }

    // Check if the Product table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'ProductTable'");
    $stmt->execute();

    // If the Product table does not exist, create it
    if($stmt->rowCount() == 0) {
        // SQL statement to create Product table
        $sql = "CREATE TABLE ProductTable (
            ProductID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ProductName VARCHAR(255) NOT NULL,
            Description TEXT,
            Price DECIMAL(10, 2) NOT NULL,
            Quantity INT(11) NOT NULL,
            Status CHAR(1),
            SupplierID INT(11) UNSIGNED,
            FOREIGN KEY (SupplierID) REFERENCES SupplierTable(SupplierID)
        )";

        // Execute the statement
        $pdo->exec($sql);
        echo "Product table created successfully\n";
    }

    // Check if the Inventory table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'InventoryTable'");
    $stmt->execute();

    // If the Inventory table does not exist, create it
    if($stmt->rowCount() == 0) {
        // SQL statement to create Inventory table
        $sql = "CREATE TABLE InventoryTable (
            ProductID INT(11) UNSIGNED,
            ProductName VARCHAR(255) NOT NULL,
            Quantity INT(11) NOT NULL,
            Price DECIMAL(10, 2) NOT NULL,
            Status CHAR(1),
            SupplierName VARCHAR(255) NOT NULL
        )";

        // Execute the statement
        $pdo->exec($sql);
        echo "Inventory table created successfully\n";
    }

} catch(PDOException $error) {
    // In case of an error when creating the tables, output the error
    echo $sql . "\n" . $error->getMessage();
}

?>
