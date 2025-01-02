<?php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // Define target directory for images
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/Project-I-BCA/assets/images/menu/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        // Generate unique filename
        $unique_filename = 'menu_' . uniqid() . '.' . $file_type;
        $target_file = $target_dir . $unique_filename;
        
        // Check if file is an actual image
        $check = getimagesize($file_tmp);
        if ($check !== false) {
            // Check file size (limit to 5MB)
            if ($_FILES['image']['size'] <= 5000000) {
                // Allow certain file formats
                if (in_array($file_type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    if (move_uploaded_file($file_tmp, $target_file)) {
                        // Store relative path in database
                        $image_path = 'menu/' . $unique_filename;
                        
                        // Insert into database
                        $sql = "INSERT INTO menu_items (name, description, price, image) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssds", $name, $description, $price, $image_path);
                        
                        if ($stmt->execute()) {
                            $message = "Menu item added successfully!";
                        } else {
                            $message = "Error: " . $conn->error;
                            // Delete uploaded file if database insert fails
                            unlink($target_file);
                        }
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $message = "Sorry, only JPG, JPEG, PNG & WEBP files are allowed.";
                }
            } else {
                $message = "Sorry, your file is too large. Maximum size is 5MB.";
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        $message = "Please select an image to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Menu Item</title>
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
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>Insert Menu Item</h1>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="insert_menu_item.php" method="POST" enctype="multipart/form-data">
        <label for="dish-name">Enter Dish Name:</label>
        <input type="text" id="dish-name" name="name" required><br>

        <label for="menu-description">Short Description:</label>
        <input type="text" id="menu-description" name="description" required><br>

        <label for="menu-price">Price:</label>
        <input type="number" id="menu-price" name="price" step="0.01" required><br>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br>

        <button type="submit">Add New Item</button>
    </form>
</body>
</html>
