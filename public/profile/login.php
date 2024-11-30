<?php
session_start();
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    // If the user exists
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password with the hashed password stored in the database
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to the dashboard or any other protected page
            header("Location: /Project-I-BCA/Homepage.php");//now just added template but redirect to another page
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>


<?php include '../../includes/header.php'; ?>

<h4>
<a href="register.php">Register</a>
</h4>
<br>
<br>
<br>
<h1>Login</h1>
<form action="login.php" method="POST">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>



<br>
<br>
<br>
<?php include '../../includes/footer.php'; ?>

