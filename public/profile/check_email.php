<?php
require '../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

echo json_encode(['exists' => $stmt->get_result()->num_rows > 0]); 