<?php
// footer.php
?>

<footer class="footer-main">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <a href="/Project-I-BCA/homepage.php">
                    <img src="/Project-I-BCA/assets/images/TableServetransparentmid.png" alt="Logo">
                </a>
            </div>
            <div class="footer-links">
                <ul>
                    <li><a href="/Project-I-BCA/homepage.php">Home</a></li>
                    <li><a href="/Project-I-BCA/public/menu/menu_items.php">Menu</a></li>
                    <li><a href="/Project-I-BCA/public/profile/login.php">Login</a></li>
                    <li><a href="/Project-I-BCA/public/profile/register.php">Register</a></li>
                    <li><a href="/Project-I-BCA/admin/admin_login.php">Admin</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> TableServe. All rights reserved.</p>
        </div>
    </div>
</footer>
<style>
    .footer-main {
        background-color: #4CAF50; /* Green theme */
        color: #fff;
        padding: 20px 0;
    }
    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .footer-logo img {
        max-width: 150px;
    }
    .footer-links ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
    }
    .footer-links ul li {
        margin: 0 10px;
    }
    .footer-links ul li a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s;
    }
    .footer-links ul li a:hover {
        color: #ddd;
    }
    .footer-social a {
        color: #fff;
        margin: 0 10px;
        font-size: 18px;
        transition: color 0.3s;
    }
    .footer-social a:hover {
        color: #ddd;
    }
    .footer-bottom {
        text-align: center;
        margin-top: 20px;
    }
</style>

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
</body>
</html>
