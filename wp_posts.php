<?php
header('Content-Type: application/json');
// Database connection parameters
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "fullpropeta"; 

// Connect to MySQL
$link = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Check if ID parameter is provided in the URL
if(isset($_GET['id'])) {
    // Sanitize the ID parameter
    $id = mysqli_real_escape_string($link, $_GET['id']);

    // SQL query to retrieve data by ID
    $sql = "SELECT * FROM wp_posts WHERE id = $id";

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
                    $row[$key] = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $row[$key]);
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

} else { // If ID parameter is not provided, retrieve all records
    // SQL query to retrieve all data
    $sql = "SELECT * FROM wp_posts ORDER BY id DESC LIMIT 500";

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
                    $row[$key] = preg_replace('/[^\p{L}\p{N}\s.]/u', '', $row[$key]);
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

// Close connection
mysqli_close($link);
?>
