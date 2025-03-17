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
    <style>
        /* Table styling */
.table-container {
    width: 100%;
    overflow-x: auto; /* Enables horizontal scrolling */
    max-height: 400px;
    border: 1px solid #ccc;
    padding: 5px;
    background-color: #f9f9f9;
    white-space: nowrap; /* Prevents text wrapping in table cells */
}

/* Ensure table is wider than the container */
table {
    width: 100%;
    min-width: 900px; /* Adjust as needed */
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

        /* Search Form */
        .search_form {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            max-width: 400px;
            margin: 20px auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .search_form select, .search_form button {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        .search_form button {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .search_form button:hover {
            background-color: #0056b3;
        }
        /* Update Status Form */
        .update_status_form {
            text-align: center;
            margin-top: 20px;
        }
        .update_status_form select, .update_status_form button {
            padding: 8px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .update_status_form button {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .update_status_form button:hover {
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
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a class="active" href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>


    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            <li><a  href="po_view_events.php"> View Events</a></li>
            <li><a  href="po_view_leave_application.php">  View Leave Application</a></li>
                <li><a class="active"  href="po_view_grievance.php">View Grievance</a></li>
            </ul>
        </div>
        <div class="widget">
    <div class="container">
        <h1 style="text-align: center;">View Grievances</h1>

        <!-- Search Form -->
        <form class="search_form" method="post">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="PENDING" <?= ($status_filter == "PENDING") ? "selected" : "" ?>>Pending</option>
                <option value="RESOLVED" <?= ($status_filter == "RESOLVED") ? "selected" : "" ?>>Resolved</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <!-- Grievance Table -->
        <form method="post">
            <div class="table-container">
                <?php if (!empty($grievances)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>ID</th>
                                <th>Unit</th>
                                <th>Activity Type</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Send To</th>
                                <th>Attachment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grievances as $row): ?>
                                <tr>
                                    <td><input type="checkbox" name="selected_grievances[]" value="<?= $row['grievance_id'] ?>"></td>
                                    <td><?= htmlspecialchars($row['grievance_id']) ?></td>
                                    <td><?= htmlspecialchars($row['unit']) ?></td>
                                    <td><?= htmlspecialchars($row['activity_type']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td><?= htmlspecialchars($row['body']) ?></td>
                                    <td><?= htmlspecialchars($row['send_to']) ?></td>
                                    <td>
                                        <?php if (!empty($row['photo_pdf_path'])): ?>
                                            <a href="<?= htmlspecialchars($row['photo_pdf_path']) ?>" download>Download</a>
                                        <?php else: ?>
                                            No Attachment
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center;">No grievances found.</p>
                <?php endif; ?>
            </div>

            <!-- Update Status Form -->
            <div class="update_status_form">
                <label for="new_status">Change Status To:</label>
                <select name="new_status">
                    <option value="PENDING">Pending</option>
                    <option value="RESOLVED">Resolved</option>
                </select>
                <button type="submit" name="update_status">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
