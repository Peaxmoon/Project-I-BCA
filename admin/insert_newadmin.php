<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

session_start();  // Start the session

// Check if the user is logged in by verifying if `admin_id` exists in the session
if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: admin_login.php"); 
    exit();  // Ensure no further code is executed
}

// Example data pratik enter new data for making new admin
$name = 'pratik';
$email = 'pratik@gmail.com';
$password = 'pratik'; // Plain text password (to be hashed)
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

// Insert into the admins table
$sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

if (mysqli_query($conn, $sql)) {
    echo "Admin created successfully!";
    header("Location: admindashboard.php");
} else {
    echo "Error: " . mysqli_error($conn);
}



mysqli_close($conn); // Close the database connection

?>