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
    // Handling POST request to ADD PROPERTY
    if($method === 'POST'){
        // Get the JSON data from the request body
        $json = file_get_contents('php://input'); // Read the raw POST data sent from Postman/APP
        $data = json_decode($json, true);

        // Check if all fields are present
        if(!isset($data['userId'], $data['name'], $data['phone'], $data['email'])){
            echo json_encode(array('message' => 'Required: userId, name, phone, email, All fields (userId, name, email, phone, roles, purpose, propertyType, location, latitude, longitude, propertyTitle, propertyDescription, city, district, division,  price, areaSize, bedrooms, bathrooms, image, features, completion'));
            http_response_code(400); // Bad Request
            exit();
        }

        // Collect Data from JSON isset($data['']) ? : null;
        $userId= isset($data['userId']) ? mysqli_real_escape_string($link, $data['userId']) : null;
        $name = isset($data['name']) ? mysqli_real_escape_string($link, $data['name']) : null;
        $email = isset($data['email']) ? mysqli_real_escape_string($link, $data['email']) : null;
        $phone = isset($data['phone']) ? mysqli_real_escape_string($link, $data['phone']) : null;
        $roles = isset($data['roles']) ? mysqli_real_escape_string($link, $data['roles']) : null;
        $purpose = isset($data['purpose']) ? mysqli_real_escape_string($link, $data['purpose']) : null; 
        $propertyType = isset($data['propertyType']) ? mysqli_real_escape_string($link, $data['propertyType']) : null;
        $location = isset($data['location']) ? mysqli_real_escape_string($link, $data['location']) : null; 
        $division = isset($data['division']) ? mysqli_real_escape_string($link, $data['division']) : null;
        $district = isset($data['district']) ? mysqli_real_escape_string($link, $data['district']) : null;
        $city = isset($data['city']) ? mysqli_real_escape_string($link, $data['city']) : null;
        $latitude = isset($data['latitude']) ? mysqli_real_escape_string($link, $data['latitude']) : null;
        $longitude = isset($data['longitude']) ? mysqli_real_escape_string($link, $data['longitude']) : null;
        $propertyTitle = isset($data['propertyTitle']) ? mysqli_real_escape_string($link, $data['propertyTitle']) : null; 
        $propertyDescription = isset($data['propertyDescription']) ? mysqli_real_escape_string($link, $data['propertyDescription']) : null;
        $price = isset($data['price']) ? mysqli_real_escape_string($link, $data['price']) : null; 
        $areaSize = isset($data['areaSize']) ? mysqli_real_escape_string($link, $data['areaSize']) : null;
        $bedrooms = isset($data['bedrooms']) ? (int)mysqli_real_escape_string($link, $data['bedrooms']) : null;
        $bathrooms = isset($data['bathrooms']) ? (int)mysqli_real_escape_string($link, $data['bathrooms']) : null; 
        $image = json_encode($data['image']); // Convert image data to JSON format
        $features = isset($data['features']) ? mysqli_real_escape_string($link, $data['features']) : null;
        $completion = isset($data['completion']) ? mysqli_real_escape_string($link, $data['completion']) : null;

        // SQL Insertion into Database
        $sql = "INSERT INTO re_properties (user_id, user_name, email, phone, roles, type, property_type, location, division_name, district_name, city_name, latitude, longitude, name, description, price, square, number_bedroom, number_bathroom, images, content, completion_status) VALUES ('$userId', '$name', '$email', '$phone', '$roles', '$purpose', '$propertyType', '$location', '$division', '$district', '$city', '$latitude', '$longitude', '$propertyTitle', '$propertyDescription', '$price', '$areaSize', '$bedrooms', '$bathrooms', '$image', '$features', '$completion')";

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Property added successfully.'));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not add property.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }
?>
