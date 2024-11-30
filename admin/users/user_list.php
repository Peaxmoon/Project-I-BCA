<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: ../admin_login.php"); 
    exit();  // Ensure no further code is executed
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<h2>User Management</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
