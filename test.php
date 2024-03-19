<?php
    header('Content-Type: application/json');

    //setting up database
    $host = "localhost"; //domain name
    $username = "root"; //mysql username
    $password = ""; //password to login in mysql
    $database = "mmart"; //database name

    //connect to mysql
    $link = mysqli_connect($host, $username, $password, $database);
    //check connection
    if($link == false){
        die("Error: Could not Connect. " . mysqli_connect_error() );
    }

    //Handling get request to fetch data 
    if($_SERVER['REQUEST_METHOD'] === 'GET'){ //Checking the HTTP METHOD if it's GET
        $sql= "SELECT *  FROM  products ORDER BY id " ; // $sql = retrieving data form database using sql command 
        $result = mysqli_query($link, $sql); // runs query after checking db connection

        if (mysqli_num_rows($result) > 0) { //_num_rows checks data row and returns if > 0
            $products = array(); // initialize an empty array
            while ($row = mysqli_fetch_assoc($result)) { //fetch_assoc gets the corrent row & move the pointer to next
                $products[] = $row; //storing the result from row to a variable
            }
            echo json_encode($products); //encode the array to json format and show output
        } else {
            echo json_encode(array('message' => 'No products found.')); 
        }

    }

    // Handling POST request to create a new product
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input'); //read the raw POST data sent from Postman
        $data = json_decode($json, true); //decode the JSON data into an associative array ($data)

        // Check if all required fields are present
        if (!isset($data['id'], $data['title'], $data['description'], $data['image'], $data['quantity'], $data['category'], $data['price'])) {
            echo json_encode(array('message' => 'All fields (id, title, description, image, quantity, category, price) are required.'));
            http_response_code(400); // Bad request
            exit();
        }

        // Collect data from JSON
        $id = mysqli_real_escape_string($link, $data['id']);
        $title = mysqli_real_escape_string($link, $data['title']);
        $description = mysqli_real_escape_string($link, $data['description']);
        $image = mysqli_real_escape_string($link, $data['image']);
        $quantity = mysqli_real_escape_string($link, $data['quantity']);
        $category = mysqli_real_escape_string($link, $data['category']);
        $price = mysqli_real_escape_string($link, $data['price']);
        $discount_price = isset($data['discount_price']) ? mysqli_real_escape_string($link, $data['discount_price']) : null;

        // Insert into database
        $sql = "INSERT INTO products (id, title, description, image, quantity, category, price, discount_price)
                VALUES ('$id', '$title', '$description', '$image', '$quantity', '$category', '$price', '$discount_price')";

        if (mysqli_query($link, $sql)) {
            echo json_encode(array('message' => 'Product added successfully.'));
            http_response_code(201); // Created
        } else {
            echo json_encode(array('message' => 'Error: Could not add product.' . mysqli_error($link)));
            http_response_code(500); // Internal Server Error
        }
    }

?>