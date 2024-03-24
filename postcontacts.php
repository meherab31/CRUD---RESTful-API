<?php
    header('Content-Type: application/json');

    //setting up database
    $host = "localhost"; //domain name
    $username = "root"; //mysql username
    $password = ""; //password to login in mysql
    $database = "propeta"; //database name

    //connect to mysql
    $link = mysqli_connect($host, $username, $password, $database);
    //check connection
    if($link == false){
        die("Error: Could not Connect. " . mysqli_connect_error() );
    }

    // Handling POST request to create a new product
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input'); //read the raw POST data sent from Postman
        $data = json_decode($json, true); //decode the JSON data into an associative array ($data)

        // Check if all required fields are present
        if (!isset($data['name'], $data['email'], $data['phone'], $data['address'], $data['subject'], $data['content'])) {
            echo json_encode(array('message' => 'All fields (name, email, phone, address, subject, content) are required.'));
            http_response_code(400); // Bad request
            exit();
        }

        // Collect data from JSON
        $name = mysqli_real_escape_string($link, $data['name']);
        $email = mysqli_real_escape_string($link, $data['email']);
        $phone = mysqli_real_escape_string($link, $data['phone']);
        $address = mysqli_real_escape_string($link, $data['address']);
        $subject = mysqli_real_escape_string($link, $data['subject']);
        $content = mysqli_real_escape_string($link, $data['content']);
        $status = 0; // Default status

        // Insert into database
        $sql = "INSERT INTO contacts (name, email, phone, address, subject, content, status)
                VALUES ('$name', '$email', '$phone', '$address', '$subject', '$content', '$status')";

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Post added successfully.'));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not add post.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }
?>
