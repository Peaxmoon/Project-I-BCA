<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_number = $_POST['table_number'];
    $location = $_POST['location'];

    // Simple SQL query
    $sql = "INSERT INTO tables (table_number, location) VALUES ($table_number, '$location')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to table_list.php
        header("Location: /Project-I-BCA/admin/tables/table_list.php");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>

<h2>Add Table</h2>
<form action="" method="POST">
    <label>Table Number:</label><br>
    <input type="number" name="table_number" required><br><br>

    <label>Location:</label><br>
    <input type="text" name="location" required><br><br>

    <button type="submit">Add Table</button>
</form>
