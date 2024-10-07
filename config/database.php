<?php

$server = "localhost";
$username = "root";
$password = "";
$database = "tableserve";

// Establish a connection to the MySQL database using MySQLi
$conn = mysqli_connect($server, $username, $password, $database);

// Check if the connection was successful
if ($conn) {
    echo "Connection established";
} else {
    die("Connection failed: " . mysqli_connect_error());
}
