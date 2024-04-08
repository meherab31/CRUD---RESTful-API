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

    //Handling simple get request to fetch data 
    // if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'){ //Checking the HTTP METHOD if it's GET
    //     $sql= "SELECT *  FROM  re_accounts ORDER BY id " ; // $sql = retrieving data form database using sql command 
    //     $result = mysqli_query($link, $sql); // runs query after checking db connection

    //     if (mysqli_num_rows($result) > 0) { //_num_rows checks data row and returns if > 0
    //         $re_accounts = array(); // initialize an empty array
    //         while ($row = mysqli_fetch_assoc($result)) { //fetch_assoc gets the corrent row & move the pointer to next
    //             $re_accounts[] = $row; //storing the result from row to a variable
    //         }
    //         echo json_encode($re_accounts); //encode the array to json format and show output
    //     } else {
    //         echo json_encode(array('message' => 'No re_accounts found.')); 
    //     }

    // }

    
     //isset($_SERVER['REQUEST_METHOD']) setting the request method
    if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'){ 
        // Check if ID parameter is provided in the URL
    if (isset($_GET['id'])) {
        // Sanitize the ID parameter
        $id = mysqli_real_escape_string($link, $_GET['id']);
    
        // SQL query to retrieve data by ID
        $sql = "SELECT * FROM re_accounts WHERE id = $id";
    
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
        $sql = "SELECT * FROM re_accounts ORDER BY id ASC LIMIT 500";
    
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

        // Collect PUT data first_name, last_name, description, gender, email, phone, password, dob, credits
        $first_name = isset($data['first_name']) ? mysqli_real_escape_string($link, $data['first_name']) : null;
        $last_name = isset($data['last_name']) ? mysqli_real_escape_string($link, $data['last_name']) : null;
        $description = isset($data['description']) ? mysqli_real_escape_string($link, $data['description']) : null;
        $gender = isset($data['gender']) ? mysqli_real_escape_string($link, $data['gender']) : null;
        $phone = isset($data['phone']) ? mysqli_real_escape_string($link, $data['phone']) : null;
        $dob = isset($data['dob']) ? mysqli_real_escape_string($link, $data['dob']) : null; // Date of birth
        $credits = isset($data['credits']) ? mysqli_real_escape_string($link, $data['credits']) : null;

        // Check if the id exists
        $check_query = "SELECT * FROM re_accounts WHERE id='$id'";
        $check_result = mysqli_query($link, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // Update the profile
            $update_query = "UPDATE re_accounts SET first_name='$first_name', last_name='$last_name', description='$description', gender='$gender', phone='$phone', dob='$dob', credits='$credits' WHERE token='$token'";
            if (mysqli_query($link, $update_query)) {
                echo json_encode(array('message' => 'profile updated successfully.'));
            } else {
                echo json_encode(array('message' => 'Error: Could not update profile.' . mysqli_error($link)));
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(array('message' => 'profile with ID ' . $id . ' does not exist.'));
            http_response_code(404); // Not Found
        }
    }

    // Handling DELETE request to delete a profile
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Check if 'id' parameter is provided
        if (isset($_GET['id'])) {
            $id = mysqli_real_escape_string($link, $_GET['id']);

            // SQL query to delete data by ID
            $delete_query = "DELETE FROM re_accounts WHERE id = $id";

            // Execute the delete query
            if (mysqli_query($link, $delete_query)) {
                echo json_encode(array('message' => 'profile deleted successfully.'));
            } else {
                echo json_encode(array('message' => 'Error: Could not delete profile.' . mysqli_error($link)));
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(array('message' => 'ID parameter is required for DELETE operation.'));
            http_response_code(400); // Bad request
        }
    }   

?>