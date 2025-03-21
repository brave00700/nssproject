<?php
require_once __DIR__ . "/../config_db.php";

// Load environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

// Check session and ensure PO is logged in with unit assigned
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

$unit = $_SESSION['unit'];  // Get PO's unit from session

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch work_done_diary records for the PO's unit with search filter
$work_done_entries = [];
$unit_filter = isset($_POST['unit']) ? $_POST['unit'] : "";
$sql = "SELECT id, event_name, event_date, venue, work_done, beneficiaries, Unit FROM work_done_diary WHERE Unit = ?";
$params = [$unit];
$types = "s";

if (!empty($unit_filter)) {
    $sql .= " AND Unit = ?";
    $params[] = $unit_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $work_done_entries[] = $row;
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
            <li><a class="active" href="po_work_done_diary.php">Work Done Diary</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="container">
                <h1>Work Done Diary </h1>

                <!-- Search Form -->
                <form method="post" class="search-form">
                    <label for="unit">Filter by Unit:</label>
                    <select name="unit" id="unit">
                        <option value="">All</option>
                        <option value="1" <?= ($unit_filter === "1") ? "selected" : "" ?>>Unit 1</option>
                        <option value="2" <?= ($unit_filter === "2") ? "selected" : "" ?>>Unit 2</option>
                        <option value="3" <?= ($unit_filter === "3") ? "selected" : "" ?>>Unit 3</option>
                        <option value="4" <?= ($unit_filter === "4") ? "selected" : "" ?>>Unit 4</option>
                        <option value="5" <?= ($unit_filter === "5") ? "selected" : "" ?>>Unit 5</option>
                    </select>
                    <button type="submit">Search</button>
                </form>

                <!-- Work Done Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Venue</th>
                                <th>Work Done</th>
                                <th>Beneficiaries</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($work_done_entries as $entry): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entry['id']) ?></td>
                                    <td><?= htmlspecialchars($entry['event_name']) ?></td>
                                    <td><?= htmlspecialchars($entry['event_date']) ?></td>
                                    <td><?= htmlspecialchars($entry['venue']) ?></td>
                                    <td><?= htmlspecialchars($entry['work_done']) ?></td>
                                    <td><?= htmlspecialchars($entry['beneficiaries']) ?></td>
                                    <td><?= htmlspecialchars($entry['Unit']) ?></td>
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
