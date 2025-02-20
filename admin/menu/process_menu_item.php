<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /Project-I-BCA/admin/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);

    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        $_SESSION['error'] = "All fields are required and price must be greater than 0";
        header("Location: insert_menu_item.php");
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = 'menu_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadDir = "../../assets/images/menu/";
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            $image = "menu/" . $fileName;
            
            try {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsi", $name, $description, $price, $image, $category_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Menu item added successfully!";
                    header("Location: menu_list.php");
                    exit();
                } else {
                    throw new Exception("Error inserting menu item");
                }
            } catch (Exception $e) {
                // Delete uploaded image if database insertion fails
                if (file_exists($uploadDir . $fileName)) {
                    unlink($uploadDir . $fileName);
                }
                $_SESSION['error'] = "Database Error: " . $e->getMessage();
                header("Location: insert_menu_item.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Failed to upload image";
            header("Location: insert_menu_item.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please select an image";
        header("Location: insert_menu_item.php");
        exit();
    }
} else {
    header("Location: insert_menu_item.php");
    exit();
}
?> 