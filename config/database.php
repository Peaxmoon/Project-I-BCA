<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tableserve";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection silently - don't echo anything
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die();
}
