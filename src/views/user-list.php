<?php include '../includes/header.php'; ?>
<?php include '../includes/header.php'; ?>

<h1>User List</h1>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= $user['name'] ?></li>
    <?php endforeach; ?>
</ul>
<a href="/public/index.php?action=create">Add User</a>

<?php include '../includes/footer.php'; ?>
