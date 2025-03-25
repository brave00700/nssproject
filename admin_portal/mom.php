<?php
require_once __DIR__ . '/../config_db.php';

// Load environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch database credentials
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

// Fetch all MoM records from the database
$sql = "SELECT id, meeting_date, time, venue, Unit, attendees, agenda, recorder, discussion, decisions, created_at FROM mom_records";
$result = $conn->query($sql);

$mom_records = [];
while ($row = $result->fetch_assoc()) {
    $mom_records[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minutes of Meeting Records</title>
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
            <li><a class="active" href="mom.php">Minutes of Meeting Records</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a href="work_done_diary.php">Work Done Diary</a></li>
          </ul>
        </div>
        <div class="widget">
            <div class="container">
                <h1>Minutes of Meeting Records</h1>

                <!-- Search Form -->
                <div class="search-form">
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
                    <button onclick="exportCSV()">Export Filtered CSV</button>
                </div>

                <div class="table-container">
                    <table id="mom-table">
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
                        <tbody id="mom-table-body">
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
</div>

<script>
// JavaScript-based Filtering
function applyFilters() {
    const unitFilterValue = document.getElementById('unit-filter').value.toLowerCase();
    const tableRows = document.querySelectorAll('#mom-table tbody tr');

    tableRows.forEach(row => {
        const unitValue = row.querySelector('td:nth-child(5)').innerText.toLowerCase();
        row.style.display = unitFilterValue === "" || unitValue === unitFilterValue ? "" : "none";
    });
}

// CSV Export Function for Filtered Data
function exportCSV() {
    let csvContent = '';
    const tableRows = document.querySelectorAll('#mom-table tr');

    tableRows.forEach(row => {
        const visible = row.style.display !== 'none';
        if (visible || row === tableRows[0]) {
            const cols = row.querySelectorAll('td, th');
            const rowData = Array.from(cols).map(col => col.innerText.replace(/,/g, '')).join(",");
            csvContent += rowData + "\n";
        }
    });

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'filtered_mom_records.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>

</body>
</html>
