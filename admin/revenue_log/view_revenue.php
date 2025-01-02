<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    // If accessed directly, redirect to dashboard
    header("Location: ../admindashboard.php?page=revenue");
    exit();
}

// Fetch revenue data
$sql = "SELECT o.*, u.name as user_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.status = 'completed' 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

// Calculate total revenue
$total_revenue = 0;
$monthly_revenue = array();
$daily_revenue = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_revenue += $row['total_price'];
        
        // Monthly revenue
        $month = date('Y-m', strtotime($row['created_at']));
        if (!isset($monthly_revenue[$month])) {
            $monthly_revenue[$month] = 0;
        }
        $monthly_revenue[$month] += $row['total_price'];
        
        // Daily revenue
        $day = date('Y-m-d', strtotime($row['created_at']));
        if (!isset($daily_revenue[$day])) {
            $daily_revenue[$day] = 0;
        }
        $daily_revenue[$day] += $row['total_price'];
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Revenue Report</h2>
    </div>

    <!-- Revenue Summary -->
    <div class="revenue-summary">
        <div class="summary-card">
            <h3>Total Revenue</h3>
            <p class="amount">Rs. <?php echo number_format($total_revenue, 2); ?></p>
        </div>
        <div class="summary-card">
            <h3>This Month</h3>
            <p class="amount">Rs. <?php echo number_format($monthly_revenue[date('Y-m')] ?? 0, 2); ?></p>
        </div>
        <div class="summary-card">
            <h3>Today</h3>
            <p class="amount">Rs. <?php echo number_format($daily_revenue[date('Y-m-d')] ?? 0, 2); ?></p>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="revenue-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result->data_seek(0); // Reset result pointer
                while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['user_name'] ?? 'Guest'); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                    <td>Rs. <?php echo number_format($row['total_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>