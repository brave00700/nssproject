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

// Storing session variable
if(!$_SESSION['po_id'] || !$_SESSION['unit']){
    header("Location: ../login.html");
}  



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_grievances']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_grievances = $_POST['selected_grievances'];
        
        // Update status in the database
        $ids = implode(",", array_map('intval', $selected_grievances));
        $sql_update = "UPDATE grievance SET status = ? WHERE grievance_id IN ($ids)";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("s", $new_status);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch grievances based on search filter
$grievances = [];
$status_filter = isset($_POST['status']) ? $_POST['status'] : "";

$unit = $_SESSION['unit'];

$sql = "SELECT grievance_id, unit, activity_type, subject, body, send_to, photo_pdf_path, status 
        FROM grievance 
        WHERE send_to IN ('BOTH', 'PO') 
        AND unit = ?";

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($status_filter)) {
    $stmt->bind_param("ss", $unit, $status_filter);
} else {
    $stmt->bind_param("s", $unit);
}

$stmt->execute();
$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {
    $grievances[] = $row;
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
            <li><a href="po_budget.php">Budget</a></li>
            <li><a href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
   
</div>

<script src="script.js"></script>
</body>
</html>
