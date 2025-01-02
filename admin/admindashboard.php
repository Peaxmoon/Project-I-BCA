<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

session_start();  // Start the session

// Check if the user is logged in by verifying if `admin_id` exists in the session
if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: admin_login.php"); 
    exit();  // Ensure no further code is executed
}

// Fetch statistics
$stats = [
    'total_orders' => 0,
    'total_users' => 0,
    'total_menu_items' => 0,
    'total_revenue' => 0
];

// Get total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Get total users (all users from users table)
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Get total menu items
$result = $conn->query("SELECT COUNT(*) as count FROM menu_items");
$stats['total_menu_items'] = $result->fetch_assoc()['count'];

// Get total revenue
$result = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$row = $result->fetch_assoc();
$stats['total_revenue'] = $row['total'] ?? 0;

// If the user is logged in, display the dashboard content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Project-I-BCA/assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-logo">
                <img src="/Project-I-BCA/assets/images/tableservetransparentmid.png" alt="TableServe Logo">
            </div>
            <div class="sidebar-menu">
                <a href="?page=dashboard" class="action-btn <?php echo (!isset($_GET['page']) || $_GET['page'] === 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="?page=menu" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'menu') ? 'active' : ''; ?>">
                    <i class="fas fa-utensils"></i>
                    <span>Manage Menu</span>
                </a>
                <a href="?page=users" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="?page=orders" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'orders') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Manage Orders</span>
                </a>
                <a href="?page=tables" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'tables') ? 'active' : ''; ?>">
                    <i class="fas fa-chair"></i>
                    <span>Manage Tables</span>
                </a>
                <a href="?page=revenue" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'revenue') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Revenue Report</span>
                </a>
                <a href="?page=profile" class="action-btn <?php echo (isset($_GET['page']) && $_GET['page'] === 'profile') ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="admin_logout.php" class="action-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-container">
                <!-- Admin Header -->
                <div class="admin-header">
                    <div class="admin-welcome">
                        <div class="admin-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="admin-title">
                            <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
                            <p>Manage your restaurant operations here</p>
                        </div>
                    </div>
                </div>

                <?php if (!isset($_GET['page']) || $_GET['page'] === 'dashboard'): ?>
                    <!-- Statistics Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon orders-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $stats['total_orders']; ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon users-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $stats['total_users']; ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon menu-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $stats['total_menu_items']; ?></h3>
                                <p>Menu Items</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon revenue-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="stat-info">
                                <h3>Rs. <?php echo number_format($stats['total_revenue']); ?></h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Main Content Area -->
                <div class="main-content">
                    <?php
                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        switch($page) {
                            case 'tables':
                                if (isset($_GET['action'])) {
                                    $action = $_GET['action'];
                                    if ($action === 'add') {
                                        include './tables/insert_table.php';
                                    } elseif ($action === 'update' && isset($_GET['id'])) {
                                        include './tables/update_table.php';
                                    } elseif ($action === 'delete' && isset($_GET['id'])) {
                                        include './tables/delete_table.php';
                                    } else {
                                        echo "Invalid action!";
                                    }
                                } else {
                                    include './tables/table_list.php';
                                }
                                break;
                            case 'menu':
                                define('INCLUDED_FROM_DASHBOARD', true);
                                include './menu/menu_list.php';
                                break;
                            case 'users':
                                include './users/user_list.php';
                                break;
                            case 'orders':
                                include './orders/orders_list.php';
                                break;
                            case 'revenue':
                                define('INCLUDED_FROM_DASHBOARD', true);
                                include './revenue_log/view_revenue.php';
                                break;
                            case 'profile':
                                define('INCLUDED_FROM_DASHBOARD', true);
                                include './profile/admin_profile.php';
                                break;
                            case 'dashboard':
                                // Show dashboard stats
                                break;
                            default:
                                echo "<div class='admin-message error'>Page not found!</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php include '../includes/footer.php'; ?>

