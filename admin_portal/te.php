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

// Handle status and comment update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resolve_grievance'])) {
    $grievance_id = intval($_POST['grievance_id']);
    $comment = trim($_POST['comment']);

    $sql_update = "UPDATE grievance SET status = 'RESOLVED', comment = ? WHERE grievance_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $comment, $grievance_id);
    $stmt_update->execute();
    $stmt_update->close();
}

// Fetch grievances from the database
$grievances = [];
$sql = "SELECT grievance_id, unit, activity_type, subject, body, send_to, photo_pdf_path, status, comment FROM grievance WHERE send_to IN ('BOTH', 'ADMIN')";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $grievances[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Grievance Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
       /* Center the filter dropdown */
.filter-container {
    margin: 20px 0;
    display: flex;
    justify-content: center;  /* Center the dropdown */
}

.filter-dropdown {
    padding: 8px;
    
    border-radius: 5px;
    font-size: 16px;
    
    background-color: #f9f9f9;
}

/* Table Styling */
.table-container {
    width: 100%;
    overflow-x: auto;
    max-height: 400px;
    border: 1px solid #ccc;
    padding: 5px;
    background-color: #f9f9f9;
}

table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
}

table th {
    background-color: #007bff;
    color: white;
}

/* Resolve Button - New Style */
.resolve-btn {
    padding: 8px 16px;
    background-color:rgb(77, 196, 4);  /* Bright orange button */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.resolve-btn:hover {
    background-color:rgb(65, 152, 3);  /* Slightly darker hover effect */
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    max-width: 90%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.modal-content textarea {
    width: 100%;
    height: 100px;
    margin-top: 10px;
    padding: 10px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.modal-content button {
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content button:hover {
    background-color: #218838;
}

    </style>
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
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                        <li><a class="active" href="manage_more.php"> More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            
            <li><a  href="view_events.php">Events</a></li>
            <li><a class="active" href="view_grievances.php">Grievances</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>

            <li><a href="manage_images.php">Upload Images to gallery</a></li>
            
            </ul>
        </div>
        <div class="widget">
<div class="container">
    <h1 style="text-align: center;">View Grievances</h1>

    <div class="filter-container">
        <select id="statusFilter" class="filter-dropdown">
            <option value="ALL">All</option>
            <option value="PENDING">Pending</option>
            <option value="RESOLVED">Resolved</option>
        </select>
    </div>

    <div class="table-container">
        <table id="grievanceTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Unit</th>
                    <th>Activity Type</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Send To</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Resolve</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grievances as $row): ?>
                    <tr data-status="<?= $row['status'] ?>">
                        <td><?= htmlspecialchars($row['grievance_id']) ?></td>
                        <td><?= htmlspecialchars($row['unit']) ?></td>
                        <td><?= htmlspecialchars($row['activity_type']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['body']) ?></td>
                        <td><?= htmlspecialchars($row['send_to']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['comment'] ?? 'No comment yet') ?></td>
                        <td><button class="resolve-btn" data-id="<?= $row['grievance_id'] ?>">Resolve</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="commentModal" class="modal">
    <div class="modal-content">
        <h3>Add a Comment and Resolve</h3>
        <form method="post">
            <textarea name="comment" placeholder="Write your comment here..." required></textarea>
            <input type="hidden" name="grievance_id" id="grievanceId">
            <button type="submit" name="resolve_grievance">Submit</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.resolve-btn').forEach(button => {
        button.addEventListener('click', function () {
            const grievanceId = this.getAttribute('data-id');
            document.getElementById('grievanceId').value = grievanceId;
            document.getElementById('commentModal').style.display = 'flex';
        });
    });

    // Close the modal on click outside
    window.onclick = function (event) {
        if (event.target == document.getElementById('commentModal')) {
            document.getElementById('commentModal').style.display = 'none';
        }
    };

    // Filter table rows based on status
    document.getElementById('statusFilter').addEventListener('change', function () {
        const selectedStatus = this.value;
        const rows = document.querySelectorAll('#grievanceTable tbody tr');
        rows.forEach(row => {
            row.style.display = (selectedStatus === 'ALL' || row.getAttribute('data-status') === selectedStatus) ? '' : 'none';
        });
    });
</script>

</body>
</html>
