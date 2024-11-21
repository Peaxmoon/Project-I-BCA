<?php
include '../../config/database.php';

$table_number = $_POST['table_number'];
$location = $_POST['location'];

$sql = "INSERT INTO tables (table_number, location) VALUES ($table_number, '$location')";

if ($conn->query($sql) === TRUE) {
    echo "New table added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
