<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Validate status value
    $valid_statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Order ID {$order_id} status updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Failed to update status for Order ID {$order_id}.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Invalid status value!</p>";
    }
}

// Fetch all orders
$sql = "SELECT orders.id, users.name AS user_name, tables.table_number, orders.total_price, orders.status 
        FROM orders 
        JOIN users ON orders.user_id = users.id
        JOIN tables ON orders.table_id = tables.id
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);
?>

<h2>Order Management</h2>
<table border="1">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Table</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['table_number']; ?></td>
                <td><?php echo $row['total_price']; ?></td>
                <td>
                <form method="POST">
    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
    <select name="status" required>
        <option value="pending" <?php echo ($row['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="in_progress" <?php echo ($row['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
        <option value="completed" <?php echo ($row['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="cancelled" <?php echo ($row['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
    </select>

                </td>
                <td>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
