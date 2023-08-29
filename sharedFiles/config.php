<?php
// database details 
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "db_files"; 
 
// creating a connection 
$conn = mysqli_connect($host, $username, $password, $dbname); 

// to ensure that the connection is made 
if (!$conn) { 
    die("Connection failed!" . mysqli_connect_error()); 
} 
?>