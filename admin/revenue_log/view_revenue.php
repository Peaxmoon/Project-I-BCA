<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: /Project-I-BCA/admin/login.php");
    exit();
}

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

// Base query
$sql = "SELECT rl.*, o.table_id, u.name as customer_name
        FROM revenue_logs rl
        JOIN orders o ON rl.order_id = o.id
        JOIN users u ON o.user_id = u.id
        WHERE DATE(rl.transaction_date) BETWEEN ? AND ?";

$params = [$start_date, $end_date];
$types = "ss";

if ($payment_method) {
    $sql .= " AND rl.payment_method = ?";
    $params[] = $payment_method;
    $types .= "s";
}

$sql .= " ORDER BY rl.transaction_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$total_revenue = 0;
$payment_methods = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .export-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Revenue Report</h1>

    <div class="filters">
        <form method="GET">
            <label>Start Date:
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </label>
            <label>End Date:
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </label>
            <label>Payment Method:
                <select name="payment_method">
                    <option value="">All Methods</option>
                    <option value="khalti" <?php echo $payment_method === 'khalti' ? 'selected' : ''; ?>>Khalti</option>
                    <option value="cash" <?php echo $payment_method === 'cash' ? 'selected' : ''; ?>>Cash</option>
                </select>
            </label>
            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Table</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): 
                $total_revenue += $row['amount'];
                $payment_methods[$row['payment_method']] = ($payment_methods[$row['payment_method'] ?? 0] ?? 0) + $row['amount'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['transaction_date']))); ?></td>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['table_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td>Rs. <?php echo htmlspecialchars(number_format($row['amount'], 2)); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['payment_method'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="summary">
        <h2>Summary</h2>
        <p>Total Revenue: Rs. <?php echo number_format($total_revenue, 2); ?></p>
        <?php foreach ($payment_methods as $method => $amount): ?>
            <p><?php echo ucfirst($method); ?>: Rs. <?php echo number_format($amount, 2); ?></p>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="export_report.php">
        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
        <button type="submit" class="export-btn">Export to Excel</button>
    </form>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>