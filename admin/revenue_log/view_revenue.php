<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Default date range for current month
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

// If custom date range is selected
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Fetch revenue logs for the selected date range
$sql = "SELECT id, amount, type, created_at FROM revenue_log 
        WHERE created_at BETWEEN ? AND ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Logs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ccc;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Revenue Logs</h1>
        <a href="admin_dashboard.php" class="button">Back to Dashboard</a>
    </header>

<br>
<br>
<br>
<br>
<br>

    <!-- Filter Form -->
    <form method="POST">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
        <button type="submit" class="button">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Amount (Rs.)</th>
                <th>Type</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No revenue data found for the selected period.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
