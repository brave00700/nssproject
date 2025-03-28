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

// Ensure the program officer is logged in and has a unit assigned
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_reports']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_reports = $_POST['selected_reports'];
        
        // Update status in the database for the selected reports
        $ids = implode(",", array_map('intval', $selected_reports));
        $sql_update = "UPDATE work_reports SET wr_status = ? WHERE wr_id IN ($ids) AND unit = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ss", $new_status, $_SESSION['unit']);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch work reports based on filters or display all for the PO's unit
$work_reports = [];
$status_filter = isset($_POST['wr_status']) ? $_POST['wr_status'] : "";

$unit = $_SESSION['unit'];  // PO's assigned unit

$sql = "SELECT wr_id, exec_id, wr_file, upload_date, unit, wr_status FROM work_reports WHERE unit = ?";
$params = [$unit];
$types = "s";

if (!empty($status_filter)) {
    $sql .= " AND wr_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $work_reports[] = $row;
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
            <li><a class="active" href="po_work_reports.php">Work Reports</a></li>
            <li><a href="po_stock_items.php">Stock Items</a></li>
            <li><a href="po_mom.php">Minutes of Meeting Records</a></li>
            <li><a href="po_budget.php">Budget</a></li>
            <li><a href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="container">
                <header>
                    <h1 >Work Reports</h1>
                </header>

                <!-- Search Form -->
                <form method="post" class="search-form">
                    <label for="wr_status">Filter by Status:</label>
                    <select name="wr_status" id="wr_status">
                        <option value="">All</option>
                        <option value="Pending" <?= $status_filter == "Pending" ? "selected" : "" ?>>Pending</option>
                        <option value="Approved" <?= $status_filter == "Approved" ? "selected" : "" ?>>Approved</option>
                        <option value="PO_Approved" <?= $status_filter == "PO_Approved" ? "selected" : "" ?>>PO Approved</option>
                    </select>
                    <button type="submit">Search</button>
                </form>

                <!-- Work Reports Table -->
                <form method="post">
                    <div class="table-container">
                        <?php if (!empty($work_reports)): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>ID</th>
                                        <th>Executive ID</th>
                                        <th>File</th>
                                        <th>Upload Date</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($work_reports as $row): ?>
                                        <tr>
                                            <td><input type="checkbox" name="selected_reports[]" value="<?= $row['wr_id'] ?>"></td>
                                            <td><?= htmlspecialchars($row['wr_id']) ?></td>
                                            <td><?= htmlspecialchars($row['exec_id']) ?></td>
                                            <td><a href="..<?= htmlspecialchars($row['wr_file']) ?>" download>Download</a></td>
                                            <td><?= htmlspecialchars($row['upload_date']) ?></td>
                                            <td><?= htmlspecialchars($row['unit']) ?></td>
                                            <td><?= htmlspecialchars($row['wr_status']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="text-align: center;">No work reports found.</p>
                        <?php endif; ?>
                    </div>
                    <div class="update-form">
                        <label for="new_status">Change Status To:</label>
                        <select name="new_status">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
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
