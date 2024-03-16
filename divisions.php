<?php
// Database connection parameters
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "propeta"; 

// Connect to MySQL
$link = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Check if 'id' parameter is set in the URL
if(isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $id = mysqli_real_escape_string($link, $_GET['id']);

    // SQL query to retrieve data for the specified ID
    $sql = "SELECT * FROM divisions WHERE id = $id";

    // Execute the query
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            // Initialize an empty array to hold the data
            $data = array();

            // Fetch and process the row
            $row = mysqli_fetch_assoc($result);

            // Sanitize each value in the row
            foreach ($row as $key => $value) {
                // Remove HTML tags
                $row[$key] = strip_tags($value);
                // Remove unwanted characters
                $row[$key] = preg_replace('/[^\p{L}\p{N}\s]/u', '', $row[$key]);
            }

            $data[] = $row; // Append the sanitized row to the $data array

            // Convert $data array to JSON
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            echo $json;
        } else {
            echo "No records found for ID: $id";
        }
    } else {
        echo "ERROR: Could not execute $sql. " . mysqli_error($link);
    }
} else {
    // If 'id' parameter is not set, fetch all division data
    $sql = "SELECT * FROM divisions ORDER BY id DESC LIMIT 500";

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
                    // Remove unwanted characters
                    $row[$key] = preg_replace('/[^\p{L}\p{N}\s]/u', '', $row[$key]);
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
