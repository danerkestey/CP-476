$(document).ready(function() {
    // Event listener for the update button on the Product table
    $('body').on('click', '.update-button', function() {
        var productID = $(this).data('id');
        var newQuantity = $(this).parent().prev().find('.update-field').val(); // Get the new quantity from the input box

        // Send the updated quantity to the server using AJAX
        $.ajax({
            url: '/CP-476/php/actions/update.php',
            type: 'POST',
            data: { productID: productID, quantity: newQuantity },
            dataType: 'json',
            success: function(data) {
                if (data.status === "success") {
                    alert(data.message); // Show success message
                    refreshData(); // Refresh the Product table to update the data
                } else {
                    alert(data.message); // Show error message
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + "; " + error);
                console.error("Response: " + xhr.responseText);
                alert('An unexpected error occurred. Please try again.');
            }
        });
    });





    // This function gets data from the server and updates the table
    function refreshData() {
        const tables = {
            Inventory: ['ProductID', 'ProductName', 'Quantity', 'Price', 'Status', 'SupplierName'],
            Product: ['UniqueID', 'ProductID', 'ProductName', 'Description', 'Price', 'Quantity', 'Status', 'SupplierID'],
            Supplier: ['SupplierID', 'SupplierName', 'Address', 'Phone', 'Email']
        };
    
        Object.keys(tables).forEach(table => {
            $.ajax({
                url: '/CP-476/php/actions/refresh.php',
                type: 'POST',
                data: { table: table },
                dataType: 'json',
                success: function (data) {
                    // Clear the table
                    $(`#${table}Table tbody`).empty();
                    $(`#${table}Table thead`).empty();
    
                    if (data.status === "success") {
                        // Generate table headers
                        let headerHtml = '<tr>';
                        tables[table].forEach(function (column) {
                            if (column === 'UniqueID') {
                                headerHtml += `<th class="hidden">${column}</th>`;
                            } else {
                                headerHtml += `<th>${column}</th>`;
                            }
                        });
    
                        // Add the "Quantity" column header
                        if (table === "Product") {
                            headerHtml += `<th>Quantity</th>`;
                            headerHtml += `<th>Update</th>`; // Add the header for the Update button
                        }
    
                        if (table !== "Inventory") {
                            headerHtml += `<th>Action</th>`;
                        }
                        headerHtml += '</tr>';
                        $(`#${table}Table thead`).append(headerHtml);
    
                        // For each row, append it to the table
                        data.results.forEach(function (row) {
                            let rowHtml = '<tr>';
                            tables[table].forEach(function (column) {
                                if (column === 'UniqueID') {
                                    rowHtml += `<td class="hidden">${row[column]}</td>`;
                                } else {
                                    rowHtml += `<td>${row[column]}</td>`;
                                }
                            });
    
                            // Add the text box for the "Quantity" column
                            if (table === "Product") {
                                rowHtml += `<td><input type="text" class="update-field" value="" /></td>`; // Leave the value blank
                                rowHtml += `<td><button class="update-button" data-id="${row['ProductID']}">Update</button></td>`; // Add the Update button
                            }
    
                            // Add a delete button at the end of each row, except for Inventory table
                            if (table !== "Inventory") {
                                let UniqueID;
                                if (table === "Supplier") {
                                    UniqueID = row['SupplierID'];
                                } else if (table === "Product") {
                                    UniqueID = row['ProductID'];
                                }
                                rowHtml += `<td><button class="delete-button" data-id="${UniqueID}">Delete Entry</button></td>`;
                            }
    
                            rowHtml += '</tr>';
                            $(`#${table}Table tbody`).append(rowHtml);
                        });
                    } else {
                        alert(data.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: " + status + "; " + error);
                    console.error("Response: " + xhr.responseText);
                }
            });
        });
    }
    

    // Call the refreshData function when the page loads
    refreshData();

    // This function sends the data from a form to the server
    function sendData(table, formData) {
        $.ajax({
            url: '/CP-476/php/actions/insert.php',
            type: 'POST',
            data: { table: table, ...formData },
            dataType: 'json',
            success: function(data) {
                console.log('Data sent to server');
                if (data.status === "success") {
                    refreshData();  // Refresh data on success
                    // Clear form fields
                    if(table === 'Product') {
                        $('#ProductForm')[0].reset();
                        // Clear any previous error message
                        $('#productErrorMessage').text('');
                    }
                    if(table === 'Supplier') {
                        $('#SupplierForm')[0].reset();
                        // Clear any previous error message
                        $('#supplierErrorMessage').text('');
                    }
                } else {
                    // Show error message
                    if (table === 'Product') {
                        $('#productErrorMessage').text(data.message);
                    } else if (table === 'Supplier') {
                        $('#supplierErrorMessage').text(data.message);
                    } else {    
                        alert(data.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Error sending data to server');
                console.error("AJAX Error: " + status + "; " + error);
                console.error("Response: " + xhr.responseText);
                // Show generic error message
                if (table === 'Product') {
                    $('#productErrorMessage').text('An unexpected error occurred. Please try again.');
                } else if (table == 'Supplier') {
                    $('#supplierErrorMessage').text('An unexpected error occurred. Please try again.');
                }
                else {
                    alert('An unexpected error occurred. Please try again.');
                }
            }
        });
    }

    // Event Listeners for the Add Product and Add Supplier buttons
    // Refreshes tables after data is inserted
    $('#ProductForm, #SupplierForm').submit(function(event) {
    console.log('Form before submission');
    event.preventDefault();
    
    var formData = $(this).serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    if (formData.hasOwnProperty('ProductForm_SupplierId')) {
        formData['SupplierID'] = formData['ProductForm_SupplierId'];
        delete formData['ProductForm_SupplierId'];
    }

    if (formData.hasOwnProperty('SupplierForm_SupplierId')) {
        formData['SupplierID'] = formData['SupplierForm_SupplierId'];
        delete formData['SupplierForm_SupplierId'];
    }
    
    console.log(formData);
    
    var table = $(this).attr('id').replace('Form', '');
    sendData(table, formData);
    console.log('Form after submission');
    });

    // Call the refreshData function when the Refresh button is clicked
    $('#refresh').click(function() {
        console.log('Refresh button clicked');
        refreshData();
    });

    // Event listener for the delete button
    $('body').on('click', '.delete-button', function() {
        var id = $(this).data('id');
        var table = $(this).parents('table').attr('id').replace('Table', '');
        $.post("/CP-476/php/actions/delete.php", { table: table, id: id }, function(data) {
            // Display a success message, or handle errors
            if(data.status === 'success') {
                alert('Entry deleted successfully');
                refreshData(); // Refresh the tables to update them
            } else {
                alert('Error: ' + data.message);
            }
        }, 'json');
    });
});