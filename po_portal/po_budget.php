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

// Ensure the Program Officer is logged in and has a unit assigned
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

$unit = intval($_SESSION['unit']);  // Fetch the unit assigned to the Program Officer

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_items']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_items = $_POST['selected_items'];

        // Update status only for the selected items of the PO's unit
        $ids = implode(",", array_map('intval', $selected_items));
        $sql_update = "UPDATE budget SET status = ? WHERE id IN ($ids) AND Unit = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $unit);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch budget records based on filters and the PO's unit
$budgets = [];
$status_filter = isset($_POST['status']) ? $_POST['status'] : "";

$sql = "SELECT id, event_name, pdf_file, status, uploaded_at, Unit FROM budget WHERE Unit = ?";
$params = [$unit];
$types = "i";

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $budgets[] = $row;
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
            <li><a href="po_stock_items.php">Stock Items</a></li>
            <li><a href="po_mom.php">Minutes of Meeting Records</a></li>
            <li><a class="active" href="po_budget.php">Budget</a></li>
            <li><a href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="container">
                <h1>Budget Records</h1>
                <form method="post" class="search-form">
                    <label for="status">Filter by Status:</label>
                    <select name="status" id="status">
                        <option value="">All</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="PO_Approved">PO Approved</option>
                    </select>
                    <button type="submit">Search</button>
                </form>

                <form method="post">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>PDF File</th>
                                    <th>Status</th>
                                    <th>Uploaded At</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($budgets as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_items[]" value="<?= $row['id'] ?>"></td>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><a href="../uploads/<?= htmlspecialchars($row['pdf_file']) ?>" target="_blank">View PDF</a></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td><?= htmlspecialchars($row['uploaded_at']) ?></td>
                                        <td><?= htmlspecialchars($row['Unit']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="update-form">
                        <label for="new_status">Change Status To:</label>
                        <select name="new_status">
                            <option value="Pending">Pending</option>
                           
                            <option value="PO_Approved">PO Approved</option>
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
