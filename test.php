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
?>