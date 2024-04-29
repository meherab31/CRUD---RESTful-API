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
        if (!isset($data['email'], $data['password'])) {
            echo json_encode(array('message' => 'Email and password are required. |Fields -> first_name,email, phone, password, |'));
            http_response_code(400); // Bad request
            exit();
        }

        // Collect data from JSON
        $first_name = isset($data['first_name']) ? mysqli_real_escape_string($link, $data['first_name']) : null;
        $email = mysqli_real_escape_string($link, $data['email']);
        $phone = isset($data['phone']) ? mysqli_real_escape_string($link, $data['phone']) : null;
        $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password

        // Generate random token
        $token = bin2hex(random_bytes(32));

        // Insert into database
        $sql = "INSERT INTO re_accounts (first_name, email, phone, password,  remember_token)
                VALUES ('$first_name', '$email', '$phone', '$password',  '$token')";

        if (mysqli_query($link, $sql)) {
            // Get the ID of the inserted record
            $user_id = mysqli_insert_id($link); //getting user id   

            // Return user ID and token in the response
            echo json_encode(array('message' => 'Account created successfully.', 'user_id' => $user_id, 'token' => $token));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not create account.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }

    // Close connection
    mysqli_close($link);
?>
