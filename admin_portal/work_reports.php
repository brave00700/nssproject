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

// Fetch all work reports from the database (this will be filtered client-side)
$work_reports = [];

$sql = "SELECT wr_id, exec_id, wr_file, upload_date, unit, wr_status FROM work_reports";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $work_reports[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Reports</title>
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
            
          <li><a class="active" href="work_reports.php">Work Reports</a></li>
            <li><a href="stock_items.php">Stock Items</a></li>
             
            <li><a href="mom.php">Minutes of Meeting Records</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a href="work_done_diary.php">Work Done Diary</a></li>

            
          </ul>
        </div>
        <div class="widget">
       
        

    <div class="container">
        <header><h1 style="text-align: center;">Work Reports</h1></header>

        <!-- Filters -->
        <form class="search-form">
            <label for="wr_status">Filter by Status:</label>
            <select id="wr_status">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="PO_Approved">PO Approved</option>
            </select>
            <label for="unit">Filter by Unit:</label>
            <select id="unit">
                <option value="">All</option>
                <option value="1">Unit 1</option>
                <option value="2">Unit 2</option>
                <option value="3">Unit 3</option>
                <option value="4">Unit 4</option>
                <option value="5">Unit 5</option>
            </select>
            <button type="button" onclick="filterTable()">Apply Filter</button>
            <button type="button" onclick="exportToCSV()">Generate CSV</button>
        </form>

        <!-- Work Reports Table -->
        <div class="table-container">
            <table id="workReportsTable">
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
        </div>
    </div>
</div>

<script>
// JavaScript for filtering table rows based on dropdown selections
function filterTable() {
    let statusFilter = document.getElementById("wr_status").value.toLowerCase();
    let unitFilter = document.getElementById("unit").value.toLowerCase();
    let table = document.getElementById("workReportsTable");
    let rows = table.getElementsByTagName("tr");

    // Loop through rows and hide those that don't match filters
    for (let i = 1; i < rows.length; i++) {
        let status = rows[i].getElementsByTagName("td")[6].innerText.toLowerCase();
        let unit = rows[i].getElementsByTagName("td")[5].innerText.toLowerCase();
        rows[i].style.display =
            (statusFilter === "" || status === statusFilter) &&
            (unitFilter === "" || unit === unitFilter)
                ? ""
                : "none";
    }
}

// Function to export visible table rows to CSV
function exportToCSV() {
    let table = document.getElementById("workReportsTable");
    let rows = table.getElementsByTagName("tr");
    let csvContent = "data:text/csv;charset=utf-8,";

    for (let i = 0; i < rows.length; i++) {
        if (rows[i].style.display !== "none") {
            let cols = rows[i].querySelectorAll("td, th");
            let csvRow = Array.from(cols)
                .map(col => col.innerText)
                .join(",");
            csvContent += csvRow + "\r\n";
        }
    }

    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "work_reports.csv");
    document.body.appendChild(link);
    link.click();
}
</script>
</body>
</html>
