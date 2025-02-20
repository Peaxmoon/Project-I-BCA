<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../admindashboard.php?page=users");
    exit();
}

$user_id = intval($_GET['id']);

// Fetch user details
$sql = "SELECT *, DATE_FORMAT(created_at, '%M %d, %Y %h:%i %p') as formatted_date 
        FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch user's orders
$orders_sql = "SELECT o.*, t.table_number,
               DATE_FORMAT(o.created_at, '%M %d, %Y %h:%i %p') as formatted_date
               FROM orders o
               LEFT JOIN tables t ON o.table_id = t.id
               WHERE o.user_id = ?
               ORDER BY o.created_at DESC LIMIT 5";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">User Details</h2>
        <a href="../admindashboard.php?page=users" class="admin-btn back-btn">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="user-details-grid">
        <div class="details-section">
            <h3><i class="fas fa-user"></i> Basic Information</h3>
            <div class="info-group">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> 
                    <span class="role-badge <?php echo $user['role']; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </p>
                <p><strong>Status:</strong> 
                    <span class="status-badge <?php echo $user['status']; ?>">
                        <?php echo ucfirst($user['status']); ?>
                    </span>
                </p>
                <p><strong>Joined:</strong> <?php echo $user['formatted_date']; ?></p>
            </div>
        </div>

        <div class="details-section">
            <h3><i class="fas fa-shopping-bag"></i> Recent Orders</h3>
            <?php if ($orders->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Table</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['table_number']; ?></td>
                                <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No orders found</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.user-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1.5rem;
}

.details-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.details-section h3 {
    color: #333;
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-group p {
    margin: 0;
    color: #666;
}

.info-group strong {
    color: #333;
    min-width: 100px;
    display: inline-block;
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

.no-data {
    color: #666;
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Inherit your existing badge styles */
.role-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.role-badge.admin { background-color: #4CAF50; color: white; }
.role-badge.user { background-color: #2196F3; color: white; }
.status-badge.active { background-color: #4CAF50; color: white; }
.status-badge.inactive { background-color: #dc3545; color: white; }
.status-badge.pending { background-color: #ffc107; color: #000; }
.status-badge.completed { background-color: #28a745; color: white; }
.status-badge.cancelled { background-color: #dc3545; color: white; }
</style> 