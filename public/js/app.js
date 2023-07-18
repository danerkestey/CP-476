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

    // Call the refreshData function when the Refresh button is clicked
    $('#refresh').click(function() {
        refreshData();
    });
});
