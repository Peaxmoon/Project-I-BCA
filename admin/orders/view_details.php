<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../admindashboard.php?page=orders");
    exit();
}

$order_id = intval($_GET['id']);

// Fetch order details with formatted date
$order_sql = "SELECT o.*, u.name as user_name, t.table_number,
              DATE_FORMAT(o.created_at, '%M %d, %Y %h:%i %p') as formatted_date
              FROM orders o
              JOIN users u ON o.user_id = u.id
              JOIN tables t ON o.table_id = t.id
              WHERE o.id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Fetch order items
$items_sql = "SELECT oi.*, m.name as item_name 
              FROM order_items oi
              JOIN menu_items m ON oi.menu_item_id = m.id
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Order Details #<?php echo htmlspecialchars($order['id']); ?></h2>
        <a href="../admindashboard.php?page=orders" class="admin-btn back-btn">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="admin-message success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="admin-message error">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="details-grid">
        <div class="details-section">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
            <p><strong>Table Number:</strong> <?php echo htmlspecialchars($order['table_number']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['formatted_date']); ?></p>
        </div>

        <div class="details-section">
            <h3>Order Status</h3>
            <form method="POST" class="status-form" id="statusForm" onsubmit="updateOrderStatus(event, this)">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="status" class="status-select <?php echo $order['status']; ?>">
                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $order['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="admin-btn warning">Update Status</button>
            </form>
            <p class="mt-3"><strong>Payment Status:</strong> 
                <span class="status-badge <?php echo $order['payment_status']; ?>">
                    <?php echo ucfirst($order['payment_status']); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="table-responsive mt-4">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                    <td><strong>₹<?php echo number_format($order['total_price'], 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="status-message" class="status-message"></div>

<style>
/* Updated styles to match website theme */
.admin-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
}

.admin-card-title {
    font-size: 1.5rem;
    color: #333;
    margin: 0;
}

.back-btn {
    background-color: #007BFF;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #0056b3;
}

.details-section {
    background-color: white;
    border: 1px solid #ddd;
    padding: 1.5rem;
    border-radius: 5px;
}

.admin-btn.warning {
    background-color: #ffc107;
    color: #000;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.admin-btn.warning:hover {
    background-color: #e0a800;
}

/* Status badges styling */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge.paid {
    background-color: #4CAF50;
    color: white;
}

.status-badge.pending {
    background-color: #ffc107;
    color: #000;
}

/* Table styling */
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.admin-table th {
    background-color: #f8f9fa;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
}

.admin-table td {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.total-row {
    background-color: #f8f9fa;
    font-weight: bold;
}

/* Status select styling */
.status-select {
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #ddd;
    min-width: 150px;
    cursor: pointer;
}

/* Keeping your existing status colors */
.status-select.pending {
    background-color: #ffc107;
    color: #000;
}

.status-select.in_progress {
    background-color: #17a2b8;
    color: white;
}

.status-select.completed {
    background-color: #28a745;
    color: white;
}

.status-select.cancelled {
    background-color: #dc3545;
    color: white;
}

.status-message {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 5px;
    display: none;
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
}

.status-message.success {
    background-color: #4CAF50;
    color: white;
}

.status-message.error {
    background-color: #f44336;
    color: white;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<script>
function showMessage(message, type) {
    const messageDiv = document.getElementById('status-message');
    messageDiv.textContent = message;
    messageDiv.className = `status-message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}

function updateOrderStatus(event, form) {
    event.preventDefault();
    
    if (!confirm('Are you sure you want to update this order status?')) {
        return;
    }

    const formData = new FormData(form);
    
    fetch('/Project-I-BCA/admin/orders/update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Update the select element's class to reflect new status
            const statusSelect = form.querySelector('.status-select');
            statusSelect.className = 'status-select ' + formData.get('status');
        } else {
            showMessage(data.message || 'Error updating status', 'error');
        }
    })
    .catch(error => {
        showMessage('Error updating status', 'error');
        console.error('Error:', error);
    });
}
</script> 