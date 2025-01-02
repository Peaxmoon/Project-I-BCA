<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: profile/login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch recent orders
$stmt = $conn->prepare("
    SELECT o.*, GROUP_CONCAT(CONCAT(mi.name, ' x', oi.quantity)) as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-header {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-welcome h1 {
            font-family: 'Playfair Display', serif;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .user-welcome p {
            color: #666;
        }

        .dashboard-actions {
            display: flex;
            gap: 1rem;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .primary-btn {
            background: #4CAF50;
            color: white;
        }

        .secondary-btn {
            background: #2196F3;
            color: white;
        }

        .dashboard-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2196F3;
        }

        .card-title {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .orders-table th {
            font-weight: 600;
            color: #333;
            background: #f8f9fa;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }

        .quick-action-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #4CAF50;
        }

        .quick-action-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .dashboard-actions {
                flex-direction: column;
                width: 100%;
            }

            .dashboard-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="user-welcome">
                <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <p>Manage your orders and preferences</p>
            </div>
            <div class="dashboard-actions">
                <a href="menu/menu_items.php" class="dashboard-btn primary-btn">
                    <i class="fas fa-utensils"></i> View Menu
                </a>
                <a href="orders/my_receipt.php" class="dashboard-btn secondary-btn">
                    <i class="fas fa-receipt"></i> My Orders
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h2 class="card-title">Quick Actions</h2>
                </div>
                <div class="quick-actions">
                    <a href="orders/order_confirmation.php" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>View Cart</span>
                    </a>
                    <a href="orders/my_table_receipt.php" class="quick-action-btn">
                        <i class="fas fa-table"></i>
                        <span>Table Receipt</span>
                    </a>
                    <a href="profile/profile.php" class="quick-action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>Edit Profile</span>
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h2 class="card-title">Order Statistics</h2>
                </div>
                <!-- Add order statistics here -->
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h2 class="card-title">Recent Orders</h2>
            </div>
            <div class="table-responsive">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                <td>Rs. <?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>