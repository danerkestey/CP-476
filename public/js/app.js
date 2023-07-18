$(document).ready(function() {

    // This function gets data from the server and updates the table
function refreshData() {
    const tables = {
        Inventory: ['ProductID', 'ProductName', 'Quantity', 'Price', 'Status', 'SupplierName'],
        Product: ['UniqueID', 'ProductID', 'ProductName', 'Description', 'Price', 'Quantity', 'Status', 'SupplierID'],
        Supplier: ['SupplierID', 'SupplierName', 'Address', 'Phone', 'Email']
    };
    
    Object.keys(tables).forEach(table => {
        $.post('/CP-476/php/actions/refresh.php', { table: table }, function(response) {
            // Clear the table
            $(`#${table}Table tbody`).empty();

            let data = JSON.parse(response);

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
        });
    });
}

// This function sends the data from a form to the server
function sendData(table, formData) {
    $.post('/CP-476/php/actions/insert.php', { table: table, ...formData }, function(response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            refreshData();  // Refresh data on success
        } else {
            alert(data.message);
        }
    });
}

    // Event Listeners for the Add Product and Add Supplier buttons
    // Refreshes tables after data is inserted
    $('#ProductForm, #SupplierForm').submit(function(event) {
        event.preventDefault();
        
        var formData = $(this).serialize();
        var formURL = $(this).attr("action");
        var formMethod = $(this).attr("method");
        
        $.ajax({
            url: formURL,
            type: formMethod,
            data: formData,
            success: function(data, textStatus, jqXHR) {
                var response = JSON.parse(data);
                if (response.status === 'success') {
                    alert("Data inserted successfully");
                    refreshData(); // Call the refresh function
                } else {
                    alert("An error occurred: " + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("An error occurred: " + errorThrown);
            }
        });
    });

    // Call the refreshData function when the Refresh button is clicked
    $('#refresh').click(function() {
        refreshData();
    });
});
