// Using jQuery for simplicity

$(document).ready(function() {

    // This function gets data from the server and updates the table
    function refreshData() {
        $.get('../php/actions/search.php', function(data) {
            // Clear the table
            $('#productTable tbody').empty();
            // Parse the data that came back from the server
            let rows = JSON.parse(data);
            // For each row, append it to the table
            rows.forEach(function(row) {
                $('#productTable tbody').append(
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
        });
    }

    // Call the refreshData function when the page loads
    refreshData();

    // Call the refreshData function when the Refresh button is clicked
    $('#refreshButton').click(refreshData);
});
