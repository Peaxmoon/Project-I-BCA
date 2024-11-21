<?php
// Include database connection
include '../../config/database.php';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Insert into the database
    $sql = "INSERT INTO menu_items (name, description, price) VALUES ('$name', '$description', $price)";

    if ($conn->query($sql) === TRUE) {
        $message = "New menu item created successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Menu Item</title>
</head>
<body>
    <h1>Insert Menu Item</h1>

    <?php if (isset($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="insert_menu_item.php" method="POST">
        <label for="dish-name">Enter Dish Name:</label>
        <input type="text" id="dish-name" name="name" required><br><br>

        <label for="menu-description">Short Description:</label>
        <input type="text" id="menu-description" name="description" required><br><br>

        <label for="menu-price">Price:</label>
        <input type="text" id="menu-price" name="price" required><br><br>

        <button type="submit">Add New Item</button>
    </form>
</body>
</html>
