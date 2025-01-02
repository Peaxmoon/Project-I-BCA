<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    header("Location: ../admindashboard.php?page=menu");
    exit();
}

// Fetch menu items
$sql = "SELECT * FROM menu_items ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Menu Items</h2>
        <a href="menu/insert_menu_item.php" class="admin-btn">
            <i class="fas fa-plus"></i> Add New Item
        </a>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <div class="admin-message success">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <img src="/Project-I-BCA/assets/images/<?php echo htmlspecialchars($row['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>Rs. <?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <a href="menu/edit_menu_item.php?id=<?php echo $row['id']; ?>" class="admin-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="menu/delete_menu_item.php?id=<?php echo $row['id']; ?>" 
                               class="admin-btn secondary"
                               onclick="return confirm('Are you sure you want to delete this item?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
