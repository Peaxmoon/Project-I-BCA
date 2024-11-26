<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';


// Fetch all menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<h2>Menu Management</h2>
<a href="insert_menu.php">Add New Menu Item</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Dish Name</th>
            <th>Description</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['price']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
