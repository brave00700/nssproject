<?php
require_once __DIR__ . '/../config_db.php';

// Load environment variables
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

// Fetch work_done_diary records based on selected unit
$unit_filter = isset($_POST['unit']) ? $_POST['unit'] : "";

$sql = "SELECT id, event_name, event_date, venue, work_done, beneficiaries, Unit FROM work_done_diary WHERE 1=1";
$params = [];
$types = "";

if (!empty($unit_filter)) {
    $sql .= " AND Unit = ?";
    $params[] = $unit_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$work_done_entries = [];
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
            <li><a href="stock_items.php">Stock Items</a></li>
            <li><a href="mom.php">Minutes of Meeting Records</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a class="active" href="work_done_diary.php">Work Done Diary</a></li>
          </ul>
        </div>

        <div class="widget">
            <div class="container">
                <h1>Work Done Diary</h1>

                <!-- Search Form -->
                <form method="post" class="search-form">
                    <label for="unit">Filter by Unit:</label>
                    <select name="unit" id="unit-filter">
                        <option value="">All</option>
                        <option value="1">Unit 1</option>
                        <option value="2">Unit 2</option>
                        <option value="3">Unit 3</option>
                        <option value="4">Unit 4</option>
                        <option value="5">Unit 5</option>
                    </select>
                    <button type="submit">Apply Filters</button>
                    <button type="button" onclick="exportToCSV()">Generate CSV</button>
                </form>

                <!-- Work Done Table -->
                <div class="table-container">
                    <table id="work-done-table">
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
</div>

<script>
    // Export to CSV function with filtering logic
    function exportToCSV() {
        const table = document.getElementById('work-done-table');
        let csvContent = '';
        const rows = table.getElementsByTagName('tr');

        for (let row of rows) {
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
        a.download = 'work_done_diary.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
</body>
</html>
