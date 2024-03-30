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
    if(!$link){
        die("Error: Could not Connect. " . mysqli_connect_error());
    }
    $method=$_SERVER['REQUEST_METHOD'];
    //Handling POST request to ADD PROPERTY
    if($method === 'POST'){
        //Get the JSON data from the request body
        $json = file_get_contents('php://input'); //read the raw POST data sent from Postman/APP
        $data = json_decode($json, true);

        //Check if all fields are present
        if(!isset($data['name'],$data['phone'],$data['email'], $data['roles'], $data['purpose'], $data['propertyType'], $data['location'], $data['latitude'], $data['longitude'], $data['propertyTitle'], $data['propertyDescription'], $data['price'], $data['areaSize'], $data['bedrooms'], $data['bathrooms'], $data['image'], $data['features'],)){
            echo json_encode(array('message' => 'All fields (name, email, phone, roles, purpose, propertyType, location, latitude, longitude, propertyTitle, propertyDescription, price, areaSize, bedrooms, bathrooms, image, features) are required.'));
        http_response_code(400); //Bad Request
        exit();
        }

        //Collect Data from JSON
        $name = mysqli_real_escape_string($link, $data['name']);
        $email = mysqli_real_escape_string($link, $data['email']);
        $phone = mysqli_real_escape_string($link, $data['phone']);
        $roles = mysqli_real_escape_string($link, $data['roles']);
        $purpose = mysqli_real_escape_string($link, $data['purpose']); 
        $propertyType = mysqli_real_escape_string($link, $data['propertyType']);
        $location = mysqli_real_escape_string($link, $data['location']); 
        $latitude = mysqli_real_escape_string($link, $data['latitude']);
        $longitude = mysqli_real_escape_string($link, $data['longitude']);
        $propertyTitle = mysqli_real_escape_string($link, $data['propertyTitle']); 
        $propertyDescription = mysqli_real_escape_string($link, $data['propertyDescription']);
        $price = mysqli_real_escape_string($link, $data['price']); 
        $areaSize = mysqli_real_escape_string($link, $data['areaSize']);
        $bedrooms = mysqli_real_escape_string($link, $data['bedrooms']);
        $bathrooms = mysqli_real_escape_string($link, $data['bathrooms']); 
        $image  = mysqli_real_escape_string($link, $data['images']);
        $features = mysqli_real_escape_string($link, $data['features']);

        // SQL Insertion into Database
        $sql = "INSERT INTO re_properties ()"; //pending

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Property added successfully.'));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not add property.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }

    }

?>