<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Order System</title>
    <style>

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
        
        header {
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        
        header .logo {
            font-size: 2rem;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
      
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        nav ul li a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }
        
        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        
        header .auth-links a,
        header .order-now {
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        header .auth-links a {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
            margin-left: 10px;
        }
        
        header .auth-links a:hover {
            background-color: white;
            color: #ff7e5f;
        }
        
        header .order-now {
            background-color: white;
            color: #ff7e5f;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        
        header .order-now:hover {
            background-color: #feb47b;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">TableServe Restaurant</div>
        <nav>
            <ul>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
        <div class="auth-links">
            <a href="/Project-I-BCA/public/profile/login.php">Login</a>
            <a href="/Project-I-BCA/public/profile/register.php">Register</a>
        </div>
        <a href="Project-I-BCA/public/orders/orders.php">
        <button class="order-now">Orders</button>
        </a>
    </header>
