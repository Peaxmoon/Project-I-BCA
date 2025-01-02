<?php
// Include database configuration
require '../../config/database.php';

if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into the database
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        echo "Registration successful!";
        // Optionally, redirect to a login page or dashboard
        header('Location: login.php');
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include '../../includes/header.php'; ?>

        <div class="content-wrapper">
            <div class="auth-container">
                <form method="POST" action="" id="registerForm" novalidate>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" name="name" class="form-input" required>
                        </div>
                        <div class="field-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-input" required>
                        </div>
                        <div class="field-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-input" required>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar"></div>
                        </div>
                        <div class="field-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                        </div>
                        <div class="field-message"></div>
                    </div>

                    <button type="submit" class="register-btn" id="submitBtn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>

                    <!-- ... keep existing additional links ... -->
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('registerForm');
                        const nameInput = document.getElementById('name');
                        const emailInput = document.getElementById('email');
                        const passwordInput = document.getElementById('password');
                        const confirmPasswordInput = document.getElementById('confirm_password');
                        const submitBtn = document.getElementById('submitBtn');
                        const strengthBar = document.querySelector('.password-strength-bar');

                        // Validate name
                        function validateName() {
                            const name = nameInput.value.trim();
                            const formGroup = nameInput.closest('.form-group');
                            const message = formGroup.querySelector('.field-message');

                            if (name.length < 2) {
                                showError(formGroup, message, 'Name must be at least 2 characters');
                                return false;
                            }
                            showSuccess(formGroup, message, 'Looks good!');
                            return true;
                        }

                        // Validate email
                        function validateEmail() {
                            const email = emailInput.value.trim();
                            const formGroup = emailInput.closest('.form-group');
                            const message = formGroup.querySelector('.field-message');
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                            if (!emailRegex.test(email)) {
                                showError(formGroup, message, 'Please enter a valid email address');
                                return false;
                            }
                            showSuccess(formGroup, message, 'Valid email!');
                            return true;
                        }

                        // Check password strength
                        function checkPasswordStrength(password) {
                            let strength = 0;
                            if (password.length >= 6) strength++;
                            if (password.match(/[a-z]+/)) strength++;
                            if (password.match(/[A-Z]+/)) strength++;
                            if (password.match(/[0-9]+/)) strength++;
                            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;

                            strengthBar.style.width = `${(strength / 5) * 100}%`;
                            if (strength <= 2) {
                                strengthBar.className = 'password-strength-bar strength-weak';
                            } else if (strength <= 3) {
                                strengthBar.className = 'password-strength-bar strength-medium';
                            } else {
                                strengthBar.className = 'password-strength-bar strength-strong';
                            }
                            return strength;
                        }

                        // Validate password
                        function validatePassword() {
                            const password = passwordInput.value;
                            const formGroup = passwordInput.closest('.form-group');
                            const message = formGroup.querySelector('.field-message');
                            const strength = checkPasswordStrength(password);

                            if (password.length < 6) {
                                showError(formGroup, message, 'Password must be at least 6 characters');
                                return false;
                            }
                            if (strength < 3) {
                                showError(formGroup, message, 'Password is too weak');
                                return false;
                            }
                            showSuccess(formGroup, message, 'Strong password!');
                            return true;
                        }

                        // Validate confirm password
                        function validateConfirmPassword() {
                            const password = passwordInput.value;
                            const confirmPassword = confirmPasswordInput.value;
                            const formGroup = confirmPasswordInput.closest('.form-group');
                            const message = formGroup.querySelector('.field-message');

                            if (confirmPassword !== password) {
                                showError(formGroup, message, 'Passwords do not match');
                                return false;
                            }
                            showSuccess(formGroup, message, 'Passwords match!');
                            return true;
                        }

                        function showError(formGroup, element, message) {
                            formGroup.classList.remove('success');
                            formGroup.classList.add('error');
                            element.textContent = message;
                            element.className = 'field-message error';
                        }

                        function showSuccess(formGroup, element, message) {
                            formGroup.classList.remove('error');
                            formGroup.classList.add('success');
                            element.textContent = message;
                            element.className = 'field-message success';
                        }

                        // Real-time validation
                        nameInput.addEventListener('input', validateName);
                        emailInput.addEventListener('input', validateEmail);
                        passwordInput.addEventListener('input', validatePassword);
                        confirmPasswordInput.addEventListener('input', validateConfirmPassword);

                        // Check email availability
                        let emailTimeout;
                        emailInput.addEventListener('input', function() {
                            clearTimeout(emailTimeout);
                            emailTimeout = setTimeout(async function() {
                                if (validateEmail()) {
                                    const formGroup = emailInput.closest('.form-group');
                                    const message = formGroup.querySelector('.field-message');
                                    try {
                                        const response = await fetch('check_email.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({ email: emailInput.value.trim() })
                                        });
                                        const data = await response.json();
                                        if (data.exists) {
                                            showError(formGroup, message, 'Email already registered');
                                        }
                                    } catch (error) {
                                        console.error('Error checking email:', error);
                                    }
                                }
                            }, 500);
                        });

                        // Form submission
                        form.addEventListener('submit', async function(e) {
                            e.preventDefault();

                            const isNameValid = validateName();
                            const isEmailValid = validateEmail();
                            const isPasswordValid = validatePassword();
                            const isConfirmPasswordValid = validateConfirmPassword();

                            if (isNameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid) {
                                submitBtn.classList.add('loading');
                                submitBtn.disabled = true;

                                try {
                                    const formData = new FormData(form);
                                    const response = await fetch(form.action, {
                                        method: 'POST',
                                        body: formData
                                    });

                                    if (response.ok) {
                                        window.location.href = 'login.php?registered=true';
                                    } else {
                                        throw new Error('Registration failed');
                                    }
                                } catch (error) {
                                    console.error('Error:', error);
                                    alert('Registration failed. Please try again.');
                                } finally {
                                    submitBtn.classList.remove('loading');
                                    submitBtn.disabled = false;
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </div>
</body>
</html>
