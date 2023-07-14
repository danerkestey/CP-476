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
        $.post('../../php/actions/login.php', {username: username, password: password}, function(data) {
            // Handle response from the server
            if (data === 'success') {
                // If login successful, redirect to the app page
                window.location.href = 'app.html';
            } else {
                // If login failed, display an error message
                $('#message').text('Login failed. Please check your username and password and try again.');
            }
        });
    });
});
