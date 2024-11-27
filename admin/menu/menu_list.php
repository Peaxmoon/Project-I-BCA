<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Fetch menu items from the database
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        nav {
            background-color: #333;
            color: white;
            padding: 10px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .menu-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .menu-table th, .menu-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .menu-table th {
            background-color: #4CAF50;
            color: white;
        }

        .menu-table tr:hover {
            background-color: #f1f1f1;
        }

        .menu-table td img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action-links a {
            color: #007BFF;
            text-decoration: none;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .delete-link {
            color: #FF0000;
        }

        .delete-link:hover {
            color: #FF4D4D;
        }
        .message {
    padding: 10px;
    margin: 15px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    font-size: 16px;
}

    </style>
</head>
<body>
<?php if (isset($_GET['message'])) : ?>
    <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
<?php endif; ?>


<nav>
    <ul>
        <li><a href="/Project-I-BCA/admin/admindashboard.php">Dashboard</a></li>
        <li><a href="/Project-I-BCA/admin/menu/menu_list.php">Menu Full view</a></li>
        <li><a href="/Project-I-BCA/admin/menu/insert_menu_item.php">Add item to menu</a></li>
    </ul>
</nav>

<h2>Menu List</h2>

<?php
if ($result->num_rows > 0) {
    // If there are items in the menu, display them
    echo "<table class='menu-table'>";
    echo "<thead>
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Created Date</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
          </thead>";
    echo "<tbody>";

    // Loop through each menu item and display it
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>" . htmlspecialchars($row['price']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
                <td><img src='/uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='menu-image'></td>
                <td class='action-links'>
                    <a href='/Project-I-BCA/admin/menu/update_menu.php?item_id=" . $row['id'] . "' class='update-link'>Update</a> | 
                    <a href='/Project-I-BCA/admin/menu/delete_menu.php?item_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this item?\")' class='delete-link'>Delete</a>
                </td>
            </tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No menu items found.</p>";
}

$conn->close();
?>

</body>
</html>
