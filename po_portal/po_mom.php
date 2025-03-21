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

// Ensure the program officer is logged in and the unit session is set
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unit from session
$unit = $_SESSION['unit'];

// Fetch MoM records based on the unit filter
$unit_filter = isset($_POST['unit']) ? $_POST['unit'] : "";
$sql = "SELECT id, meeting_date, time, venue, Unit, attendees, agenda, recorder, discussion, decisions, created_at 
        FROM mom_records 
        WHERE Unit = ?";
$params = [$unit];
$types = "s";

if (!empty($unit_filter) && $unit_filter == $unit) {
    $sql .= " AND Unit = ?";
    $params[] = $unit_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$mom_records = [];
while ($row = $result->fetch_assoc()) {
    $mom_records[] = $row;
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
            <li><a class="active" href="po_mom.php">Minutes of Meeting Records</a></li>
            <li><a href="po_budget.php">Budget</a></li>
            <li><a href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="container">
                <h1>Minutes of Meeting Records</h1>

                

                <!-- MoM Records Table -->
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th>Unit</th>
                            <th>Attendees</th>
                            <th>Agenda</th>
                            <th>Recorder</th>
                            <th>Discussion</th>
                            <th>Decisions</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($mom_records as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['meeting_date']) ?></td>
                                <td><?= htmlspecialchars($row['time']) ?></td>
                                <td><?= htmlspecialchars($row['venue']) ?></td>
                                <td><?= htmlspecialchars($row['Unit']) ?></td>
                                <td><?= htmlspecialchars($row['attendees']) ?></td>
                                <td><?= htmlspecialchars($row['agenda']) ?></td>
                                <td><?= htmlspecialchars($row['recorder']) ?></td>
                                <td><?= htmlspecialchars($row['discussion']) ?></td>
                                <td><?= htmlspecialchars($row['decisions']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
   
</div>

<script src="script.js"></script>
</body>
</html>
