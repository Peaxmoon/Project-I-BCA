<?php
session_start();
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: /Project-I-BCA/admin/admin_login.php"); 
    exit();  // Ensure no further code is executed
}
// Check if an `item_id` is provided
if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']); // Ensure `item_id` is an integer

    // Fetch the menu item details
    $sql = "SELECT * FROM menu_items WHERE id = $item_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get updated data from the form
            $item_name = $_POST['item_name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            // Handle image upload if provided
            $image = $item['image']; // Keep the current image if no new one is uploaded
            if (!empty($_FILES['image']['name'])) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif']; // Allowed MIME types
                $file_type = mime_content_type($_FILES['image']['tmp_name']);
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($file_type, $allowed_types) && in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
                    $target_file = $target_dir . basename($_FILES['image']['name']);

                    // Move the uploaded file to the server directory
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // Remove the old image if a new one is uploaded
                        if (!empty($image) && file_exists($target_dir . $image)) {
                            unlink($target_dir . $image);
                        }
                        $image = basename($_FILES['image']['name']); // Save the new image name
                    } else {
                        $message = "Failed to upload the image.";
                    }
                } else {
                    $message = "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
                }
            }

            // Update the menu item in the database if no errors
            if (empty($message)) {
                $update_sql = "UPDATE menu_items 
                               SET name = '$item_name', 
                                   description = '$description', 
                                   price = $price, 
                                   image = '$image' 
                               WHERE id = $item_id";

                if ($conn->query($update_sql) === TRUE) {
                    header("Location: menu_list.php?message=" . urlencode("Menu item updated successfully!"));
                    exit;
                } else {
                    $message = "Error updating item: " . $conn->error;
                }
            }
        }
    } else {
        $message = "Menu item not found!";
    }
} else {
    $message = "No menu item specified!";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Menu Item</title>
    <style>
        .menu-image-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li><a href="/Project-I-BCA/admin/admindashboard.php">Dashboard</a></li>
            <li><a href="/Project-I-BCA/admin/menu/menu_list.php">Menu</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Update Menu Item</h2>
        <?php if (empty($item)) {
            echo "Item data not found or invalid item ID.";
        } ?>

        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (!empty($item)) : ?>
            <form action="update_menu.php?item_id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data">
                <label for="item_name">Item Name:</label><br>
                <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item['name']); ?>" required><br><br>

                <label for="description">Description:</label><br>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea><br><br>

                <label for="price">Price:</label><br>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" step="0.01" required><br><br>

                <label for="image">Image (optional, JPEG/PNG/GIF only):</label><br>
                <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif"><br>
                <?php if (!empty($item['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $item['image'])) : ?>
                    <img src="/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Menu Image" class="menu-image-preview"><br>
                <?php else : ?>
                    <p>No image available.</p>
                <?php endif; ?>


                <button type="submit">Update Item</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>