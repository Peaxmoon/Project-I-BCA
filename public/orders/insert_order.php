<?php
// Include the database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not logged in
    header("Location: /Project-I-BCA/public/profile/login.php?redirect_to=/Project-I-BCA/homepage.php");
    exit();
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Check if table number is set via cookie
if (!isset($_COOKIE['table_number'])) {
    echo "Table number not set! Please scan the QR code to book a table.";
    exit();
}

// Retrieve the table number from the cookie
$table_id = intval($_COOKIE['table_number']); // Ensure it's an integer

// Check if an item ID is passed in the query string
if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']); // Ensure it's an integer

    // Fetch the details of the selected menu item
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Calculate the total price (assume quantity is 1 for simplicity)
        $total_price = $item['price'];
        $status = 'pending'; // Initial order status

        // Insert the order into the orders table
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, table_id, total_price, status) VALUES (?, ?, ?, ?)");
        $order_stmt->bind_param("iids", $user_id, $table_id, $total_price, $status);

        if ($order_stmt->execute()) {
            // Retrieve the newly created order ID
            $order_id = $order_stmt->insert_id;

            // Insert the ordered item into the order_items table
            $quantity = 1; // Assuming quantity is 1
            $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
            $order_item_stmt->bind_param("iii", $order_id, $item_id, $quantity);

            if ($order_item_stmt->execute()) {
                // Redirect to the order confirmation page
                header("Location: order_confirmation.php?order_id=$order_id");
                exit();
            } else {
                echo "Error adding item to order: " . $conn->error;
            }
        } else {
            echo "Error placing order: " . $conn->error;
        }
    } else {
        echo "Menu item not found!";
    }
} else {
    echo "No item selected!";
}

$conn->close();
?>
