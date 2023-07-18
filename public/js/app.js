$(document).ready(function() {

    // This function gets data from the server and updates the table
    function refreshData(searchTerm = '') {
        $.post('/CP-476/php/actions/search.php', { searchTerm: searchTerm }, function(response) {
            // Clear the table
            $('#inventoryTable tbody').empty();

            let data = JSON.parse(response);

            if (data.status === "success") {
                // For each row, append it to the table
                data.results.forEach(function(row) {
                    $('#inventoryTable tbody').append(
                        `<tr>
                            <td>${row.ProductID}</td>
                            <td>${row.ProductName}</td>
                            <td>${row.Quantity}</td>
                            <td>${row.Price}</td>
                            <td>${row.Status}</td>
                            <td>${row.SupplierName}</td>
                        </tr>`
                    );
                });
            } else {
                alert(data.message);
            }
        });
    }

    // Call the refreshData function when the page loads
    refreshData();

    // Call the refreshData function when the Refresh button is clicked
    $('#refresh').click(function() {
        let searchTerm = $('#searchInput').val();
        refreshData(searchTerm);
    });
});
