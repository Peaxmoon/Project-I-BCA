<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    header("Location: ../admindashboard.php?page=tables");
    exit();
}

// Fetch tables with their status
$sql = "SELECT t.*, 
        COUNT(DISTINCT o.id) as active_orders,
        COALESCE(SUM(o.total_price), 0) as total_earnings
        FROM tables t
        LEFT JOIN orders o ON t.id = o.table_id AND o.status != 'completed'
        GROUP BY t.id
        ORDER BY t.table_number";
$result = $conn->query($sql);
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Table Management</h2>
        <div class="header-actions">
            <input type="text" id="tableSearch" class="search-input" placeholder="Search tables...">
            <select id="statusFilter" class="filter-select">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="reserved">Reserved</option>
            </select>
            <button onclick="addNewTable()" class="admin-btn primary">
                <i class="fas fa-plus"></i> Add Table
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table" id="tablesTable">
            <thead>
                <tr>
                    <th>Table #</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Active Orders</th>
                    <th>Today's Earnings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($table = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($table['table_number']); ?></td>
                        <td><?php echo htmlspecialchars($table['capacity']); ?> persons</td>
                        <td>
                            <span class="status-badge <?php echo $table['status']; ?>">
                                <?php echo ucfirst($table['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $table['active_orders']; ?></td>
                        <td>₹<?php echo number_format($table['total_earnings'], 2); ?></td>
                        <td class="action-buttons">
                            <button onclick="viewTableOrders(<?php echo $table['id']; ?>)"
                                    class="admin-btn info" title="View Orders">
                                <i class="fas fa-receipt"></i>
                            </button>
                            <button onclick="editTable(<?php echo $table['id']; ?>)"
                                    class="admin-btn warning" title="Edit Table">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="toggleTableStatus(<?php echo $table['id']; ?>, '<?php echo $table['status']; ?>')"
                                    class="admin-btn primary" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Inheriting admin card styles */
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

/* Status badge colors */
.status-badge.available {
    background-color: #28a745;
    color: white;
}

.status-badge.occupied {
    background-color: #dc3545;
    color: white;
}

.status-badge.reserved {
    background-color: #ffc107;
    color: #000;
}

/* Responsive table */
.table-responsive {
    overflow-x: auto;
    padding: 1rem;
}

/* Action buttons */
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
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.admin-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.admin-btn.primary { background-color: #007bff; }
.admin-btn.info { background-color: #17a2b8; }
.admin-btn.warning { background-color: #ffc107; color: #000; }
</style>

<script>
function filterTables() {
    const searchTerm = document.getElementById('tableSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#tablesTable tbody tr');

    rows.forEach(row => {
        const tableNumber = row.cells[0].textContent.toLowerCase();
        const status = row.cells[2].textContent.toLowerCase();
        
        const matchesSearch = tableNumber.includes(searchTerm);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}

document.getElementById('tableSearch').addEventListener('input', filterTables);
document.getElementById('statusFilter').addEventListener('change', filterTables);

function addNewTable() {
    window.location.href = 'tables/add_table.php';
}

function editTable(tableId) {
    window.location.href = `tables/edit_table.php?id=${tableId}`;
}

function viewTableOrders(tableId) {
    window.location.href = `tables/table_orders.php?id=${tableId}`;
}

function toggleTableStatus(tableId, currentStatus) {
    if (confirm('Are you sure you want to change this table\'s status?')) {
        window.location.href = `tables/toggle_status.php?id=${tableId}`;
    }
}
</script> 