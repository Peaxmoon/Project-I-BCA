<!-- update_table.php -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Table ID to update
    $table_number = $_POST['table_number'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Update query
    $sql = "UPDATE tables SET table_number = $table_number, location = '$location', status = '$status' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect to table_list.php after update
        header("Location: /Project-I-BCA/admin/tables/table_list.php");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Check if 'id' is set to load current table data for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current table details
    $sql = "SELECT * FROM tables WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Error: Table not found.";
        $conn->close();
        exit();
    }
} else {
    echo "Error: Table ID not provided.";
    $conn->close();
    exit();
}
?>

<h2>Update Table</h2>
<form action="update_table.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

    <label>Table Number:</label><br>
    <input type="number" name="table_number" value="<?php echo $row['table_number']; ?>" required><br><br>

    <label>Location:</label><br>
    <input type="text" name="location" value="<?php echo $row['location']; ?>" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="available" <?php echo ($row['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
        <option value="occupied" <?php echo ($row['status'] === 'occupied') ? 'selected' : ''; ?>>Occupied</option>
    </select><br><br>

    <button type="submit">Update Table</button>
</form>
