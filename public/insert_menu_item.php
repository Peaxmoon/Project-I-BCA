<?php
include '../config/database.php';

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];

$sql = "INSERT INTO menu_items (name, description, price) VALUES ('$name', '$description', $price)";

if ($conn->query($sql) === TRUE) {
    echo "New menu item created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
