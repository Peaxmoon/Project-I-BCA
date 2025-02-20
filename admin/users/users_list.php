<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    header("Location: ../admindashboard.php?page=users");
    exit();
}

// Fetch users with role and status
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="admin-message success">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="admin-message error">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">User Management</h2>
        <div class="header-actions">
            <input type="text" id="userSearch" class="search-input" placeholder="Search users...">
            <select id="roleFilter" class="filter-select">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <select id="statusFilter" class="filter-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table" id="usersTable">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $user['status']; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="users/view_details.php?id=<?php echo $user['id']; ?>" 
                               class="admin-btn info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')"
                                    class="admin-btn warning"
                                    title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button onclick="viewUserOrders(<?php echo $user['id']; ?>)"
                                    class="admin-btn primary"
                                    title="View Orders">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px;
    overflow: hidden;
}

.admin-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #ddd;
    background-color: #f8f9fa;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-input, .filter-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 150px;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.role-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.role-badge.admin {
    background-color: #4CAF50;
    color: white;
}

.role-badge.user {
    background-color: #2196F3;
    color: white;
}

.status-badge.active {
    background-color: #4CAF50;
    color: white;
}

.status-badge.inactive {
    background-color: #dc3545;
    color: white;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.admin-btn {
    padding: 0.5rem;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.admin-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.admin-btn.info { background-color: #17a2b8; }
.admin-btn.warning { background-color: #ffc107; color: #000; }
.admin-btn.primary { background-color: #007bff; }
</style>

<script>
document.getElementById('userSearch').addEventListener('input', filterUsers);
document.getElementById('roleFilter').addEventListener('change', filterUsers);
document.getElementById('statusFilter').addEventListener('change', filterUsers);

function filterUsers() {
    const searchTerm = document.getElementById('userSearch').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        const role = row.cells[3].textContent.toLowerCase();
        const status = row.cells[4].textContent.toLowerCase();
        
        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = !roleFilter || role.includes(roleFilter);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        
        row.style.display = (matchesSearch && matchesRole && matchesStatus) ? '' : 'none';
    });
}

function toggleUserStatus(userId, currentStatus) {
    if (confirm('Are you sure you want to ' + 
        (currentStatus === 'active' ? 'deactivate' : 'activate') + 
        ' this user?')) {
        window.location.href = `users/toggle_status.php?id=${userId}`;
    }
}

function viewUserOrders(userId) {
    window.location.href = `users/user_orders.php?id=${userId}`;
}
</script> 