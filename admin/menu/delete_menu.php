<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if an `item_id` is passed via the GET method
if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']); // Get the item_id from the URL

    // Fetch the item to get its image filename (if applicable)
    $fetch_sql = "SELECT image FROM menu_items WHERE id = $item_id";
    $fetch_result = $conn->query($fetch_sql);

    if ($fetch_result->num_rows > 0) {
        $item = $fetch_result->fetch_assoc();
        $image = $item['image'];

        // Delete the menu item from the database
        $delete_sql = "DELETE FROM menu_items WHERE id = $item_id";

        if ($conn->query($delete_sql) === TRUE) {
            // Remove the image file from the server if it exists
            if (!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $image)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $image);
            }
            $message = "Menu item deleted successfully!";
        } else {
            $message = "Error deleting item: " . $conn->error;
        }
    } else {
        $message = "Menu item not found!";
    }
} else {
    $message = "No menu item specified!";
}

// Close the database connection
$conn->close();

// Redirect back to the menu list page with a message
header("Location: /Project-I-BCA/admin/menu/menu_list.php?message=" . urlencode($message));
exit;
?>
