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
    if($link == false){
        die("Error: Could not Connect. " . mysqli_connect_error());
    }

    // Handling POST request to create a new account
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input'); // Read the raw POST data sent from Postman
        $data = json_decode($json, true); // Decode the JSON data into an associative array ($data)

        // Check if all required fields are present
        // if (!isset($data['first_name'], $data['last_name'], $data['dob'], $data['email'], $data['phone'], $data['password'])) {
        //     echo json_encode(array('message' => 'All fields (first_name, last_name, date of birth {YYYY-MM-DD}, email, phone, password) are required.'));
        //     http_response_code(400); // Bad request
        //     exit();
        // }

        // Collect data from JSON
        $first_name = mysqli_real_escape_string($link, $data['first_name']);
        $last_name = mysqli_real_escape_string($link, $data['last_name']);
        $email = mysqli_real_escape_string($link, $data['email']);
        $phone = mysqli_real_escape_string($link, $data['phone']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password
        $dob = mysqli_real_escape_string($link, $data['dob']); // Date of birth
        $credits = isset($data['credits']) ? mysqli_real_escape_string($link, $data['credits']) : "";

        // Generate random token
        $token = bin2hex(random_bytes(32));

        // Insert into database
        $sql = "INSERT INTO re_accounts (first_name, last_name, email, phone, password, dob, credits, remember_token)
                VALUES ('$first_name', '$last_name', '$email', '$phone', '$password', '$dob', '$credits', '$token')";

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Account created successfully.', 'token' => $token));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not create account.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }

    // Close connection
    mysqli_close($link);
?>
