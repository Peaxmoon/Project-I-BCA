<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Fetch all tables
$sql = "SELECT id, table_number, location, status FROM tables";
$result = $conn->query($sql);
?>

<h2>Table Management</h2>
<table border="1">
    <thead>
        <tr>
            <th>Table ID</th>
            <th>Table Number</th>
            <th>Location</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['table_number']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="update_table.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="delete_table.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this table?');">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


<?php
// If a table is clicked, fetch and display orders for that table
if (isset($_GET['table_id'])) {
    $table_id = intval($_GET['table_id']); // Get the table ID from the query parameter
    $order_sql = "SELECT orders.id, orders.total_price, orders.status, users.name AS user_name 
                  FROM orders 
                  JOIN users ON orders.user_id = users.id 
                  WHERE orders.table_id = ?";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    echo "<h3>Orders for Table ID: $table_id</h3>";
    if ($order_result->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User Name</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
        while ($order_row = $order_result->fetch_assoc()) {
            echo "<tr>
                    <td>{$order_row['id']}</td>
                    <td>{$order_row['user_name']}</td>
                    <td>{$order_row['total_price']}</td>
                    <td>{$order_row['status']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No orders found for this table.</p>";
    }
    $stmt->close();
}
?>
