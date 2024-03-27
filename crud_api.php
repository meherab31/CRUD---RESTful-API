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

    //Handling simple get request to fetch data 
    // if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'){ //Checking the HTTP METHOD if it's GET
    //     $sql= "SELECT *  FROM  products ORDER BY id " ; // $sql = retrieving data form database using sql command 
    //     $result = mysqli_query($link, $sql); // runs query after checking db connection

    //     if (mysqli_num_rows($result) > 0) { //_num_rows checks data row and returns if > 0
    //         $products = array(); // initialize an empty array
    //         while ($row = mysqli_fetch_assoc($result)) { //fetch_assoc gets the corrent row & move the pointer to next
    //             $products[] = $row; //storing the result from row to a variable
    //         }
    //         echo json_encode($products); //encode the array to json format and show output
    //     } else {
    //         echo json_encode(array('message' => 'No products found.')); 
    //     }

    // }

    
     //isset($_SERVER['REQUEST_METHOD']) setting the request method
    if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'){ 
        // Check if ID parameter is provided in the URL
    if (isset($_GET['id'])) {
        // Sanitize the ID parameter
        $id = mysqli_real_escape_string($link, $_GET['id']);
    
        // SQL query to retrieve data by ID
        $sql = "SELECT * FROM products WHERE id = $id";
    
            // Execute the query
            if ($result = mysqli_query($link, $sql)) {
    
                if (mysqli_num_rows($result) > 0) {
                // Initialize an empty array to hold the data
                $data = array();
    
                // Fetch and process each row
                while ($row = mysqli_fetch_assoc($result)) {
                    // Sanitize each value in the row
                    foreach ($row as $key => $value) {
                        // Remove HTML tags
                        $row[$key] = strip_tags($value);
                        // Keep full stops and remove unwanted characters
                        //$row[$key] = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $row[$key]);
                    }
                    $data[] = $row; // Append each sanitized row to the $data array
                }
    
                // Convert $data array to JSON
                $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
                echo $json;
            } else {
                echo "No records matching the ID were found.";
            }
        } else {
            echo "ERROR: Could not execute $sql. " . mysqli_error($link);
        }
    
        } 
        else {
        // If ID parameter is not provided, retrieve all records
        $sql = "SELECT * FROM products ORDER BY id ASC LIMIT 500";
    
        // Execute the query
        if ($result = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($result) > 0) {
                // Initialize an empty array to hold the data
                $data = array();
    
                // Fetch and process each row
                while ($row = mysqli_fetch_assoc($result)) {
                    // Sanitize each value in the row
                    foreach ($row as $key => $value) {
                        // Remove HTML tags
                        $row[$key] = strip_tags($value);
                        // Keep full stops and remove unwanted characters
                        //$row[$key] = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $row[$key]);
                    }
                    $data[] = $row; // Append each sanitized row to the $data array
                }
    
                // Convert $data array to JSON
                $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
                echo $json;
            } else {
                echo "No records matching your query were found.";
            }
        } else {
            echo "ERROR: Could not execute $sql. " . mysqli_error($link);
        }
        }
     }


    // Handling POST request to create a new product
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

    //Handling PUT request to update data
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Check if the request contains JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Check if all required fields are present
        if (!isset($data['id'], $data['title'], $data['description'], $data['image'], $data['quantity'], $data['category'], $data['price'])) {
            echo json_encode(array('message' => 'All fields (id, title, description, image, quantity, category, price) are required.'));
            http_response_code(400); // Bad request
            exit();
        }

        // Collect PUT data
        $id = mysqli_real_escape_string($link, $data['id']);
        $title = mysqli_real_escape_string($link, $data['title']);
        $description = mysqli_real_escape_string($link, $data['description']);
        $image = mysqli_real_escape_string($link, $data['image']);
        $quantity = mysqli_real_escape_string($link, $data['quantity']);
        $category = mysqli_real_escape_string($link, $data['category']);
        $price = mysqli_real_escape_string($link, $data['price']);
        $discount_price = isset($data['discount_price']) ? mysqli_real_escape_string($link, $data['discount_price']) : null;

        // Check if the product exists
        $check_query = "SELECT * FROM products WHERE id='$id'";
        $check_result = mysqli_query($link, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // Update the product
            $update_query = "UPDATE products SET title='$title', description='$description', image='$image', 
                            quantity='$quantity', category='$category', price='$price', discount_price='$discount_price'
                            WHERE id='$id'";
            if (mysqli_query($link, $update_query)) {
                echo json_encode(array('message' => 'Product updated successfully.'));
            } else {
                echo json_encode(array('message' => 'Error: Could not update product.' . mysqli_error($link)));
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(array('message' => 'Product with ID ' . $id . ' does not exist.'));
            http_response_code(404); // Not Found
        }
    }

    // Handling DELETE request to delete a product
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Check if 'id' parameter is provided
        if (isset($_GET['id'])) {
            $id = mysqli_real_escape_string($link, $_GET['id']);

            // SQL query to delete data by ID
            $delete_query = "DELETE FROM products WHERE id = $id";

            // Execute the delete query
            if (mysqli_query($link, $delete_query)) {
                echo json_encode(array('message' => 'Product deleted successfully.'));
            } else {
                echo json_encode(array('message' => 'Error: Could not delete product.' . mysqli_error($link)));
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(array('message' => 'ID parameter is required for DELETE operation.'));
            http_response_code(400); // Bad request
        }
    }   

?>