<?php
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

// Check if Program Officer (PO) is logged in and has a unit assigned
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

$unit = intval($_SESSION['unit']); // Storing unit from session variable

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
        
        // Update status in the database for the selected items
        $ids = implode(",", array_map('intval', $selected_items));
        $sql_update = "UPDATE stock_items SET approve_status = ? WHERE id IN ($ids) AND Unit = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $unit);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch stock items based on filters or display all for the PO's assigned unit
$stock_items = [];
$category_filter = isset($_POST['category']) ? $_POST['category'] : "";
$unit_filter = $unit; // Locked to PO's assigned unit

$sql = "SELECT id, category, item_name, quantity, damaged_stock, replaced_stock, purchase_date, status, Unit, approve_status 
        FROM stock_items WHERE Unit = ?";
$params = [$unit];
$types = "i";

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $stock_items[] = $row;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Grievance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
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
      <div class="header-subtext">PROGRAM OFFICER PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a class="active" href="po_manage_reports.php">Reports & Registers</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>


    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            <li><a href="po_work_reports.php">Work Reports</a></li>
            <li><a class="active" href="po_stock_items.php">Stock Items</a></li>
            <li><a href="po_mom.php">Minutes of Meeting Records</a></li>
            <li><a href="po_budget.php">Budget</a></li>
            <li><a href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="container">
                <h1>Stock Items</h1>

                <!-- Search Form -->
                <form method="post" class="search-form">
                    <label for="category">Filter by Category:</label>
                    <select name="category" id="category">
                        <option value="">All</option>
                        <option value="Vessels">Vessels</option>
                        <option value="Housekeeping">Housekeeping</option>
                        <option value="Garden Items">Garden Items</option>
                        <option value="Office Inventory">Office Inventory</option>
                    </select>
                    <button type="submit">Search</button>
                </form>

                <!-- Stock Items Table -->
                <form method="post">
                    <div class="table-container">
                        <table>
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
                                    <th>Approval Status</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <td><?= htmlspecialchars($row['approve_status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Update Form -->
                    <div class="update-form">
                        <label for="new_status">Change Approval Status To:</label>
                        <select name="new_status">
                            <option value="APPROVED">APPROVED</option>
                            <option value="PENDING">PENDING</option>
                            <option value="PO_APPROVED">PO APPROVED</option>
                        </select>
                        <button type="submit" name="update_status">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
