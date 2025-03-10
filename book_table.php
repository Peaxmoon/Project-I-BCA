<?php
// Table Number   might be links 
//http://localhost/Project-I-BCA/book_table.php?table_number=11
//Replace 5 with the actual table number for each QR code or link.

// Start a session
session_start();

// Retrieve table_number from the URL
if (isset($_GET['table_number'])) {
    $table_number = intval($_GET['table_number']); // Ensure it's an integer

    // Set a cookie for 1 day
    setcookie('table_number', $table_number, time() + (86400), "/"); // Cookie expires in 1 day

    // Redirect to homepage.php
    header("Location: homepage.php");
    exit();
} else {
    // Redirect back or show an error if table_number is not in the URL
    echo "Invalid table number.";
    exit();
}
?>


