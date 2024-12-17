<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: /Project-I-BCA/admin/login.php");
    exit();
}

// Include PHPExcel library
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get filter parameters
$start_date = $_POST['start_date'] ?? date('Y-m-d');
$end_date = $_POST['end_date'] ?? date('Y-m-d');
$payment_method = $_POST['payment_method'] ?? '';

try {
    // Base query with payment details
    $sql = "SELECT rl.*, o.table_id, u.name as customer_name, 
            oi.menu_item_id, mi.name as item_name, oi.quantity, oi.price
            FROM revenue_logs rl
            JOIN orders o ON rl.order_id = o.id
            JOIN users u ON o.user_id = u.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE DATE(rl.transaction_date) BETWEEN ? AND ?";

    $params = [$start_date, $end_date];
    $types = "ss";

    if ($payment_method) {
        $sql .= " AND rl.payment_method = ?";
        $params[] = $payment_method;
        $types .= "s";
    }

    $sql .= " ORDER BY rl.transaction_date DESC, o.id, oi.id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Restaurant Admin')
        ->setLastModifiedBy('Restaurant Admin')
        ->setTitle('Revenue Report')
        ->setSubject('Revenue Report ' . $start_date . ' to ' . $end_date)
        ->setDescription('Revenue report generated from the restaurant management system');

    // Add headers
    $sheet->setCellValue('A1', 'Date')
        ->setCellValue('B1', 'Order ID')
        ->setCellValue('C1', 'Table')
        ->setCellValue('D1', 'Customer')
        ->setCellValue('E1', 'Item')
        ->setCellValue('F1', 'Quantity')
        ->setCellValue('G1', 'Price')
        ->setCellValue('H1', 'Total')
        ->setCellValue('I1', 'Payment Method');

    // Style the header row
    $sheet->getStyle('A1:I1')->getFont()->setBold(true);
    $sheet->getStyle('A1:I1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setRGB('CCCCCC');

    // Add data
    $rowNumber = 2;
    $totalRevenue = 0;

    while ($row = $result->fetch_assoc()) {
        $itemTotal = $row['quantity'] * $row['price'];
        $totalRevenue += $itemTotal;

        $sheet->setCellValue('A' . $rowNumber, date('Y-m-d H:i', strtotime($row['transaction_date'])))
            ->setCellValue('B' . $rowNumber, $row['order_id'])
            ->setCellValue('C' . $rowNumber, $row['table_id'])
            ->setCellValue('D' . $rowNumber, $row['customer_name'])
            ->setCellValue('E' . $rowNumber, $row['item_name'])
            ->setCellValue('F' . $rowNumber, $row['quantity'])
            ->setCellValue('G' . $rowNumber, $row['price'])
            ->setCellValue('H' . $rowNumber, $itemTotal)
            ->setCellValue('I' . $rowNumber, ucfirst($row['payment_method']));
        
        $rowNumber++;
    }

    // Add total row
    $sheet->setCellValue('A' . $rowNumber, 'Total Revenue')
        ->setCellValue('H' . $rowNumber, $totalRevenue);
    $sheet->getStyle('A' . $rowNumber . ':I' . $rowNumber)->getFont()->setBold(true);

    // Auto-size columns
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set up the response headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Revenue_Report_' . date('Y-m-d_His') . '.xlsx"');
    header('Cache-Control: max-age=0');

    // Save to output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    header("Location: /Project-I-BCA/admin/revenue_log/index.php?error=export_failed");
}

exit();
?>
