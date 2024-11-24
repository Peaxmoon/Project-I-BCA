<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Simple SQL query to delete the table
    $sql = "DELETE FROM tables WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect to table_list.php after deletion
        header("Location: /Project-I-BCA/admin/tables/table_list.php");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: Table ID not provided.";
}

$conn->close();
?>
