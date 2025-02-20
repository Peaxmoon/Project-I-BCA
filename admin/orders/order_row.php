<tr>
    <td>#<?php echo $row['id']; ?></td>
    <td><?php echo $row['order_time']; ?></td>
    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
    <td><?php echo htmlspecialchars($row['table_number']); ?></td>
    <td class="items-list" title="<?php echo htmlspecialchars(implode(', ', $order_items)); ?>">
        <?php echo htmlspecialchars(implode(', ', $order_items)); ?>
    </td>
    <td>Rs. <?php echo number_format($row['total_price'], 2); ?></td>
    <td>
        <form method="POST" class="status-form" id="form_<?php echo $row['id']; ?>" onsubmit="updateStatus(event, this)">
            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
            <div class="status-control">
                <select name="status" class="status-select <?php echo $row['status']; ?>" id="status_<?php echo $row['id']; ?>">
                    <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $row['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $row['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $row['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="action-btn update-btn">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </td>
    <td>
        <a href="/Project-I-BCA/admin/orders/view_details.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">
            <i class="fas fa-eye"></i> View
        </a>
    </td>
</tr> 