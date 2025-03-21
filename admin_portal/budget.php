<?php
require_once __DIR__ . '/../config_db.php';

// Load environment variables
loadEnv(__DIR__ . '/../.env');

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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_items']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_items = $_POST['selected_items'];
        
        $ids = implode(",", array_map('intval', $selected_items));
        $sql_update = "UPDATE budget SET status = ? WHERE id IN ($ids)";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("s", $new_status);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch budget records based on filters
$budgets = [];
$unit_filter = isset($_POST['unit']) ? $_POST['unit'] : "";
$status_filter = isset($_POST['status']) ? $_POST['status'] : "";

$sql = "SELECT id, event_name, pdf_file, status, uploaded_at, Unit FROM budget WHERE 1=1";
$params = [];
$types = "";

if (!empty($unit_filter)) {
    $sql .= " AND Unit = ?";
    $params[] = $unit_filter;
    $types .= "s";
}
if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
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
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php"> Manage Students</a></li>
           <li><a href="manage_staff.php">Manage Staff</a></li>
           <li><a class="active" href="manage_reports.php">Reports & Register</a></li>
                       <li><a href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
          <li><a href="work_reports.php">Work Reports</a></li>
            <li><a href="stock_items.php">Stock Items</a></li>
             
            <li><a href="mom.php">Minutes of Meeting Records</a></li>
            <li><a  class="active" href="budget.php">Budget</a></li>
            <li><a href="work_done_diary.php">Work Done Diary</a></li>

            
          </ul>
        </div>
        <div class="widget">
        <div class="container">
    <h1>Budget Records</h1>
    <form method="post" class="search-form">
        <label for="unit">Filter by Unit:</label>
        <select name="unit" id="unit">
            <option value="">All</option>
            <option value="1">Unit 1</option>
            <option value="2">Unit 2</option>
            <option value="3">Unit 3</option>
            <option value="4">Unit 4</option>
            <option value="5">Unit 5</option>
        </select>

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
