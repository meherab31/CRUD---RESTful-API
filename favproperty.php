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
    if(!$link){
        die("Error: Could not Connect. " . mysqli_connect_error());
    }
    $method=$_SERVER['REQUEST_METHOD'];
    // Handling POST request to ADD FAVORITE PROPERTY
    if($method === 'POST'){
        // Get the JSON data from the request body
        $json = file_get_contents('php://input'); // Read the raw POST data sent from Postman/APP
        $data = json_decode($json, true);

        // Check if all fields are present
        if(!isset($data['user_id'], $data['property_id'])){
            echo json_encode(array('message' => 'Required: user_id, property_id'));
            http_response_code(400); // Bad Request
            exit();
        }

        // Collect Data from JSON isset($data['']) ? : null;
        $userId= isset($data['user_id']) ? mysqli_real_escape_string($link, $data['user_id']) : null;
        $propertyId = isset($data['property_id']) ? mysqli_real_escape_string($link, $data['property_id']) : null;

        // SQL Insertion into Database
        $sql = "INSERT INTO re_favourite (user_id, property_id) VALUES ('$userId', '$propertyId')";

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Favorite property added successfully.'));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not add favorite property.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }

// Handling GET request to GET FAVORITE PROPERTIES for a specific user
if ($method === 'GET') {
    // Check if user_id parameter is provided in the request URL
    if (!isset($_GET['user_id'])) {
        echo json_encode(array('message' => 'Required: user_id parameter in the request URL'));
        http_response_code(400); // Bad Request
        exit();
    }

    // Retrieve user_id from the request URL
    $userId = $_GET['user_id'];

    // SQL query to retrieve favorite properties and their details for the specific user
    $sql = "SELECT rp.*
            FROM re_properties rp
            INNER JOIN re_favourite rf ON rp.id = rf.property_id
            WHERE rf.user_id = '$userId'";

    // Execute the query
    $result = mysqli_query($link, $sql);
    if ($result) {
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(array('message' => 'Error: Could not retrieve favorite properties.' . mysqli_error($link)));
        http_response_code(500); // Internal Server Error
    }
}


    // Close connection
    mysqli_close($link);
?>
