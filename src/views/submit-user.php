<?php

// Include the database connection
require '../../config/database.php';

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];

    // SQL query to insert data into the database
    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

    // Execute the query and check if the insertion was successful
    if (mysqli_query($conn, $sql)) {
        echo "New record created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
