<?php
require_once __DIR__ . '/../config_db.php';

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approve_status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_items']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_items = $_POST['selected_items'];

        // Update status in the database
        $ids = implode(",", array_map('intval', $selected_items));
        $sql_update = "UPDATE stock_items SET approve_status = ? WHERE id IN ($ids)";

        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("s", $new_status);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch all stock items (no filtering in PHP)
$stock_items = [];

$sql = "SELECT id, category, item_name, quantity, damaged_stock, replaced_stock, purchase_date, status, Unit, approve_status FROM stock_items";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $stock_items[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/report_registers.css">
</head>
<body>
<header>
    <div class="header-container">
        <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
        <div class="header-content">
            <div class="header-text">NATIONAL SERVICE SCHEME</div>
            <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
            <div class="header-subtext">ADMIN PORTAL</div>
        </div>
        <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
    </div>
</header>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    <ul>
        <li><a href="manage_applications.php">Manage Applications</a></li>
        <li><a href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_staff.php">Manage Staff</a></li>
        <li><a class="active" href="manage_reports.php">Reports & Register</a></li>
        <li><a href="manage_more.php">More</a></li>
        <li><a href="admin_logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a href="work_reports.php">Work Reports</a></li>
                <li><a class="active" href="stock_items.php">Stock Items</a></li>
                <li><a href="mom.php">Minutes of Meeting Records</a></li>
                <li><a href="budget.php">Budget</a></li>
                <li><a href="work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>

        <div class="widget">
            <div class="container">
                <h1>Stock Items</h1>

                <!-- Search Form -->
                <div class="search-form">
                    <label for="category">Filter by Category:</label>
                    <select id="category-filter">
                        <option value="">All</option>
                        <option value="Vessels">Vessels</option>
                        <option value="Housekeeping">Housekeeping</option>
                        <option value="Garden Items">Garden Items</option>
                        <option value="Office Inventory">Office Inventory</option>
                    </select>

                    <label for="unit">Filter by Unit:</label>
                    <select id="unit-filter">
                        <option value="">All</option>
                        <option value="1">Unit 1</option>
                        <option value="2">Unit 2</option>
                        <option value="3">Unit 3</option>
                        <option value="4">Unit 4</option>
                        <option value="5">Unit 5</option>
                    </select>

                    <button onclick="applyFilters()">Apply Filters</button>
                    <button onclick="exportToCSV()">Generate CSV</button>
                </div>

                <form method="post">
                    <div class="table-container">
                        <table id="stock-table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Damaged</th>
                                    <th>Replaced</th>
                                    <th>Purchase Date</th>
                                    <th>Status</th>
                                    <th>Unit</th>
                                    <th>Approval Status</th>
                                </tr>
                            </thead>
                            <tbody id="stock-table-body">
                                <?php foreach ($stock_items as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_items[]" value="<?= $row['id'] ?>"></td>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['category']) ?></td>
                                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                                        <td><?= htmlspecialchars($row['damaged_stock']) ?></td>
                                        <td><?= htmlspecialchars($row['replaced_stock']) ?></td>
                                        <td><?= htmlspecialchars($row['purchase_date']) ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td><?= htmlspecialchars($row['Unit']) ?></td>
                                        <td><?= htmlspecialchars($row['approve_status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="update-form">
                        <label for="new_status">Change Approval Status To:</label>
                        <select name="new_status">
                            <option value="APPROVED">APPROVED</option>
                            <option value="PENDING">PENDING</option>
                        </select>
                        <button type="submit" name="update_status">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const categoryFilter = document.getElementById('category-filter').value;
        const unitFilter = document.getElementById('unit-filter').value;
        const tableBody = document.getElementById('stock-table-body');
        const rows = tableBody.getElementsByTagName('tr');

        for (let row of rows) {
            const category = row.cells[2].innerText;
            const unit = row.cells[9].innerText;

            if ((categoryFilter && category !== categoryFilter) || (unitFilter && unit !== unitFilter)) {
                row.style.display = 'none';
            } else {
                row.style.display = '';
            }
        }
    }

    function exportToCSV() {
        const table = document.getElementById('stock-table');
        let csvContent = '';
        for (let row of table.rows) {
            const visible = row.style.display !== 'none';
            if (visible || row === table.rows[0]) {
                const rowData = Array.from(row.cells).map(cell => cell.innerText).join(',');
                csvContent += rowData + '\n';
            }
        }
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'stock_data.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
</body>
</html>
