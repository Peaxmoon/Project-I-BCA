<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: admindashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Admin not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --text-dark: #333;
            --text-light: #666;
            --white: #ffffff;
            --error: #dc3545;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-login-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: var(--secondary-color);
        }

        .error-message {
            color: var(--error);
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Access your restaurant dashboard</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <a href="/Project-I-BCA/Homepage.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Homepage
        </a>
    </div>
</body>
</html>
