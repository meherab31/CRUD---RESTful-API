<?php
    header('Content-Type: application/json');

    // Setting up database
    $host = "localhost"; // Domain name
    $username = "root"; // MySQL username
    $password = ""; // Password to login to MySQL
    $database = "propeta"; // Database name

    // Connect to MySQL
    $link = mysqli_connect($host, $username, $password, $database);
    // Check connection
    if ($link === false) {
        die("Error: Could not Connect. " . mysqli_connect_error());
    }

    // Handling POST request for login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Check if email and password are provided
        if (!isset($data['email'], $data['password'])) {
            echo json_encode(array('message' => 'Email and password are required.'));
            http_response_code(400); // Bad request
            exit();
        }

        // Sanitize input
        $email = mysqli_real_escape_string($link, $data['email']);
        $password = mysqli_real_escape_string($link, $data['password']);

        // SQL query to get hashed password based on email
        $sql = "SELECT id, password, remember_token FROM re_accounts WHERE email = '$email' LIMIT 1";

        $result = mysqli_query($link, $sql);

        if ($result) {
            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                $hashed_password = $row['password'];

                // Verify password
                if (password_verify($password, $hashed_password)) {
                    // Password matches
                    $token = $row['remember_token'];
                    echo json_encode(array('message' => 'Login successful.', 'token' => $token));
                    http_response_code(200); // OK
                } else {
                    // Password does not match
                    echo json_encode(array('message' => 'Invalid email or password.'));
                    http_response_code(401); // Unauthorized
                }
            } else {
                // No user found with the provided email
                echo json_encode(array('message' => 'User not found.'));
                http_response_code(404); // Not Found
            }
        } else {
            // Error executing the query
            echo json_encode(array('message' => 'Error: Could not execute query.'));
            http_response_code(500); // Internal Server Error
        }
    }

    // Close connection
    mysqli_close($link);
?>
