<?php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$message = '';

// Fetch all categories
$categories_query = "SELECT id, name FROM menu_categories";
$categories_result = $conn->query($categories_query);

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);
    $item_query = "SELECT * FROM menu_items WHERE id = $item_id";
    $item_result = $conn->query($item_query);

    if ($item_result->num_rows > 0) {
        $item = $item_result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

            // Validate inputs
            if (empty($item_name) || empty($description) || $price <= 0 || $category_id <= 0) {
                $message = "All fields are required and price must be greater than 0";
            } else {
                // Handle file upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_type = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    
                    // Generate unique filename
                    $unique_filename = 'menu_' . uniqid() . '.' . $file_type;
                    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/Project-I-BCA/assets/images/menu/";
                    
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    if (move_uploaded_file($file_tmp, $target_dir . $unique_filename)) {
                        $image_path = 'menu/' . $unique_filename;
                    } else {
                        $message = "Error uploading image file.";
                    }
                } else {
                    $image_path = $item['image'];
                }

                // Update the menu item in the database if no errors
                if (empty($message)) {
                    $update_sql = "UPDATE menu_items 
                                   SET name = ?, 
                                       description = ?, 
                                       price = ?, 
                                       image = ?, 
                                       category_id = ? 
                                   WHERE id = ?";
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param("ssdssi", $item_name, $description, $price, $image_path, $category_id, $item_id);

                    if ($stmt->execute()) {
                        header("Location: menu_list.php?message=" . urlencode("Menu item updated successfully!"));
                        exit;
                    } else {
                        $message = "Error updating item: " . $conn->error;
                    }
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Update Menu Item</h1>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="update_menu.php?id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="category">Select Category:</label>
        <select id="category" name="category_id" required>
            <option value="">Select a category</option>
            <?php while($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $item['category_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="dish-name">Enter Dish Name:</label>
        <input type="text" id="dish-name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>

        <label for="menu-description">Description:</label>
        <textarea id="menu-description" name="description" required rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>

        <label for="menu-price">Price:</label>
        <input type="number" id="menu-price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($item['price']); ?>" required>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
        <p>Current Image: <img src="/Project-I-BCA/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="Menu Image" class="menu-image-preview"></p>

        <button type="submit">Update Menu Item</button>
    </form>
</body>
</html>