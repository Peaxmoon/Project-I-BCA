<?php
session_start();
require '../config/database.php';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database (for admin table)
    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    // If the admin exists
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verify the password with the hashed password stored in the database
        if (password_verify($password, $admin['password'])) {
            // Password is correct, start a session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            // Redirect to the admin dashboard or any other protected page
            header("Location: /Project-I-BCA/admin/admindashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No admin found with that email.";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>


<br><br><br>

<h1>Admin Login</h1>

<form action="admin_login.php" method="POST">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

<br><br><br>
<?php include '../src/includes/footer.php'; ?>
