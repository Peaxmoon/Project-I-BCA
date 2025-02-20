<?php
// session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (!defined('INCLUDED_FROM_DASHBOARD')) {
    header("Location: ../admindashboard.php?page=orders");
    exit();
}

// Get today's orders
$sql = "SELECT o.*, 
        u.name as user_name, 
        t.table_number,
        DATE_FORMAT(o.created_at, '%h:%i %p') as order_time,
        oi.menu_item_id,
        mi.name as item_name,
        oi.quantity
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        LEFT JOIN tables t ON o.table_id = t.id 
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE DATE(o.created_at) = CURDATE() 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

// Process status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully!";
        header("Location: ../admindashboard.php?page=orders");
        exit();
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Today's Orders</h2>
        <div class="header-actions">
            <a href="?page=orders&view=all" class="admin-btn secondary">
                <i class="fas fa-history"></i> View All Orders
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="admin-message success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Table</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0): 
                    $current_order = null;
                    $order_items = array();
                    
                    while ($row = $result->fetch_assoc()): 
                        if ($current_order !== $row['id']) {
                            if ($current_order !== null) {
                                // Display the previous order
                                include 'order_row.php';
                            }
                            $current_order = $row['id'];
                            $order_items = array();
                        }
                        if ($row['item_name']) {
                            $order_items[] = $row['quantity'] . 'x ' . $row['item_name'];
                        }
                        
                        if ($result->num_rows === 1 || $result->num_rows === $result->current_field + 1) {
                            include 'order_row.php';
                        }
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="8" class="no-orders">No orders found for today</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="status-message" class="status-message"></div>

<style>
.admin-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px;
    overflow: hidden;
}

.admin-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
}

.status-badge.pending { background-color: #fff3e0; color: #e65100; }
.status-badge.preparing { background-color: #e3f2fd; color: #1565c0; }
.status-badge.ready { background-color: #e8f5e9; color: #2e7d32; }
.status-badge.served { background-color: #f3e5f5; color: #6a1b9a; }
.status-badge.completed { background-color: #e8f5e9; color: #2e7d32; }

.action-btn {
    padding: 6px 12px;
    border-radius: 4px;
    color: #fff;
    text-decoration: none;
    margin: 0 4px;
    cursor: pointer;
}

.view-btn {
    background: #4a6cf7;
}

.view-btn:hover {
    background: #3451b2;
}

.items-list {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.table-responsive {
    overflow-x: auto;
    padding: 1rem;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.no-orders {
    text-align: center;
    padding: 2rem;
    color: #666;
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

.status-control {
    display: flex;
    gap: 8px;
    align-items: center;
}

.status-select {
    min-width: 130px;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
    font-size: 0.9em;
}

.update-btn {
    background: #2196F3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.update-btn:hover {
    background: #1976D2;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.update-btn:active {
    transform: translateY(0);
}

.update-btn i {
    font-size: 0.9em;
}

.status-select.pending { 
    background-color: #fff3e0; 
    color: #e65100; 
    border-color: #ffe0b2;
}

.status-select.in_progress { 
    background-color: #e3f2fd; 
    color: #1565c0; 
    border-color: #bbdefb;
}

.status-select.completed { 
    background-color: #e8f5e9; 
    color: #2e7d32; 
    border-color: #c8e6c9;
}

.status-select.cancelled { 
    background-color: #ffebee; 
    color: #c62828; 
    border-color: #ffcdd2;
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

function updateStatus(event, form) {
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
            form.querySelector('.status-select').className = 'status-select ' + formData.get('status');
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
