$(document).ready(function() {
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
                success: function(data) {
                    // Clear the table
                    $(`#${table}Table tbody`).empty();
        
                    if (data.status === "success") {
                        // For each row, append it to the table
                        data.results.forEach(function(row) {
                            let rowHtml = '<tr>';
        
                            tables[table].forEach(function(column) {
                                rowHtml += `<td>${row[column]}</td>`;
                            });
        
                            rowHtml += '</tr>';
        
                            $(`#${table}Table tbody`).append(rowHtml);
                        });
                    } else {
                        alert(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + "; " + error);
                    console.error("Response: " + xhr.responseText);
                }
            });
        });
    }

    // This function sends the data from a form to the server
    function sendData(table, formData) {
        console.log('Sending data to server');
        $.ajax({
            url: '/CP-476/php/actions/insert.php',
            type: 'POST',
            data: { table: table, ...formData },
            dataType: 'json',
            success: function(data) {
                console.log('Data sent to server');
                if (data.status === "success") {
                    refreshData();  // Refresh data on success
                } else {
                    alert(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error sending data to server');
                console.error("AJAX Error: " + status + "; " + error);
                console.error("Response: " + xhr.responseText);
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
        
        var table = $(this).attr('id').replace('Form', '');
        sendData(table, formData);
        console.log('Form after submission');
    });

    // Call the refreshData function when the Refresh button is clicked
    $('#refresh').click(function() {
        console.log('Refresh button clicked');
        refreshData();
    });
});
