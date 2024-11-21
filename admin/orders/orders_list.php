<?php
include '../../config/database.php';

// Fetch all orders
$sql = "SELECT orders.id, users.name AS user_name, tables.table_number, orders.total_price, orders.status 
        FROM orders 
        JOIN users ON orders.user_id = users.id
        JOIN tables ON orders.table_id = tables.id";
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
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['table_number']; ?></td>
                <td><?php echo $row['total_price']; ?></td>
                <td><?php echo $row['status']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
