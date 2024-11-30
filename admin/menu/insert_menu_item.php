<?php
session_start();
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: /Project-I-BCA/admin/admin_login.php"); 
    exit();  // Ensure no further code is executed
}

// Define upload directory
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/'; // Use a relative path

// Ensure the directory exists, if not create it
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle file upload
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = $upload_dir . basename($image);

    // Check if file was uploaded successfully
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Check if the file is a valid image
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $valid_extensions)) {
            // Move the file to the uploads directory
            if (move_uploaded_file($image_tmp, $image_path)) {
                // Insert into database
                $sql = "INSERT INTO menu_items (name, description, price, image) VALUES ('$name', '$description', '$price', '$image')";

                if ($conn->query($sql) === TRUE) {
                    // Redirect to admin dashboard on success
                    header('Location: /Project-I-BCA/admin/admindashboard.php?page=menu');
                    exit(); // Always call exit after a header redirect
                } else {
                    $message = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $message = "Failed to move uploaded file.";
            }
        } else {
            $message = "Invalid image format. Only jpg, jpeg, png, and gif are allowed.";
        }
    } else {
        $message = "Error uploading image: " . $_FILES['image']['error'];
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

<?php if (isset($message)) : ?>
    <p class="message"><?php echo $message; ?></p>
<?php endif; ?>

<form action="insert_menu_item.php" method="POST" enctype="multipart/form-data">
    <label for="dish-name">Enter Dish Name:</label>
    <input type="text" id="dish-name" name="name" required><br>

    <label for="menu-description">Short Description:</label>
    <input type="text" id="menu-description" name="description" required><br>

    <label for="menu-price">Price:</label>
    <input type="text" id="menu-price" name="price" required><br>

    <label for="image">Upload Image:</label>
    <input type="file" id="image" name="image" accept="image/*" required><br>

    <button type="submit">Add New Item</button>
</form>

</body>
</html>
