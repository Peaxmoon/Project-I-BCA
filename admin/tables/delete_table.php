<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';


if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: ../admin_login.php"); 
    exit();  // Ensure no further code is executed
}
// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Simple SQL query to delete the table
    $sql = "DELETE FROM tables WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect to table_list.php after deletion
        header("Location: /Project-I-BCA/admin/admindashboard.php?page=tables");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: Table ID not provided.";
}

$conn->close();
?>
