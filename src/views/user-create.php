<?php include '../includes/header.php'; ?>

<h1>Create User</h1>
<form action="submit-user.php" method="POST">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required>
    
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
    
    <button type="submit">Submit</button>
</form>

<a href="/public/index.php?action=list">Back to User List</a>

<?php include '../includes/footer.php'; ?>
