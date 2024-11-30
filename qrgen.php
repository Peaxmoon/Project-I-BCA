<?php
// Include the QR Code library
include 'path-to-phpqrcode-library/qrlib.php';

// The URL to encode in the QR code
$table_number = 5; // Replace with the actual table number
$url = "https://yourwebsite.com/book_table.php?table_number=" . $table_number;

// Path to save the generated QR code image
$save_path = "qr_codes/table_" . $table_number . ".png";

// Generate the QR code and save it
QRcode::png($url, $save_path, QR_ECLEVEL_L, 10);

// Output success message with a link to the QR code
echo "QR Code for Table $table_number generated: <a href='$save_path'>View QR Code</a>";
?>
