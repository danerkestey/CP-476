$(document).ready(function() {

    // Intercept the form submission event
    $('#loginForm').submit(function(event) {
        event.preventDefault();

        // Capture form data
        let username = $('#username').val();
        let password = $('#password').val();

        // Perform basic validation
        if (username === '' || password === '') {
            $('#message').text('Please enter your username and password.');
            return;
        }

        // If validation passes, send a POST request to the login PHP script
        $.post('/CP-476/php/actions/login.php', {username: username, password: password}, function(response) {
            // If login successful, redirect to the app page
            if (response.status === 'success') {
                window.location.href = '/CP-476/public/html/app.html';
            } else {
                // If login failed, display an error message
                $('#message').text('Login failed. Please check your username and password and try again.');
            }
        }, "json") // Expect a JSON response
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Log any error messages
            console.error("Request failed: " + textStatus + ", " + errorThrown);
            console.error("Response: " + jqXHR.responseText); // REMOVE AFTER DEBUGGING
        });
    });
});
