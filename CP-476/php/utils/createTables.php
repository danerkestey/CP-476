<?php
require_once 'database.php';

try {
    // Check if the Supplier table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'SupplierTable'");
    $stmt->execute();
    
    // If the table does not exist, create it
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

    // If the table does not exist, create it
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
    // Check if the UserTable exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'UserTable'");
    $stmt->execute();

    // If the table does not exist, create it
    if($stmt->rowCount() == 0) {
        // SQL statement to create UserTable
        $sql = "CREATE TABLE UserTable (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )";

        // Execute the statement
        $pdo->exec($sql);
        echo "UserTable created successfully\n";

        // Populate the UserTable with the root user
        $rootPassword = password_hash('password', PASSWORD_DEFAULT);  // Hash the root password

        $stmt = $pdo->prepare("INSERT INTO UserTable (username, password) VALUES (:username, :password)");
        $stmt->execute([
            ':username' => 'root',
            ':password' => $rootPassword,
        ]);

        echo "Root user created\n";
    }

} catch(PDOException $error) {
    // In case of error creating the tables, output the error
    echo $sql . "\n" . $error->getMessage();
}

?>
