<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    // If accessed directly, redirect to dashboard
    header("Location: ../admindashboard.php?page=revenue");
    exit();
}

// Fetch revenue data from revenue_logs table
$sql = "SELECT rl.*, 
        o.table_id,
        u.name as user_name,
        COALESCE(rl.payment_method, 'not specified') as payment_method 
        FROM revenue_logs rl
        LEFT JOIN orders o ON rl.order_id = o.id 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY rl.transaction_date DESC";
$result = $conn->query($sql);

// Calculate total revenue
$total_revenue = 0;
$monthly_revenue = array();
$daily_revenue = array();
$payment_methods = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_revenue += $row['amount'];
        
        // Monthly revenue
        $month = date('Y-m', strtotime($row['transaction_date']));
        if (!isset($monthly_revenue[$month])) {
            $monthly_revenue[$month] = 0;
        }
        $monthly_revenue[$month] += $row['amount'];
        
        // Daily revenue
        $day = date('Y-m-d', strtotime($row['transaction_date']));
        if (!isset($daily_revenue[$day])) {
            $daily_revenue[$day] = 0;
        }
        $daily_revenue[$day] += $row['amount'];

        // Track payment methods
        $method = $row['payment_method'];
        if (!isset($payment_methods[$method])) {
            $payment_methods[$method] = 0;
        }
        $payment_methods[$method] += $row['amount'];
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

    <!-- Payment Methods Summary -->
    <div class="payment-methods-summary">
        <h3>Payment Methods Breakdown</h3>
        <?php foreach ($payment_methods as $method => $amount): ?>
            <div class="payment-method-item">
                <span class="method-name"><?php echo ucfirst(htmlspecialchars($method)); ?></span>
                <span class="method-amount">Rs. <?php echo number_format($amount, 2); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Revenue Table -->
    <div class="revenue-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Table</th>
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
                    <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name'] ?? 'Guest'); ?></td>
                    <td><?php echo htmlspecialchars($row['table_id'] ?? 'N/A'); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['transaction_date'])); ?></td>
                    <td>Rs. <?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($row['payment_method'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.payment-methods-summary {
    margin: 20px 0;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payment-method-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.payment-method-item:last-child {
    border-bottom: none;
}

.method-name {
    font-weight: 500;
    color: #333;
}

.method-amount {
    color: #4CAF50;
    font-weight: 600;
}

.revenue-summary {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.summary-card {
    flex: 1;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.summary-card h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.amount {
    font-size: 1.5em;
    color: #4CAF50;
    margin: 0;
    font-weight: 600;
}
</style>