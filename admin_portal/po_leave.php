<?php
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!empty($_POST['selected_approvals']) && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $selected_approvals = $_POST['selected_approvals'];

        // Update status in the database
        $ids = implode(",", array_map('intval', $selected_approvals));
        $sql_update = "UPDATE po_leave_approval SET status = ? WHERE approval_id IN ($ids)";

        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("s", $new_status);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

// Fetch leave approvals based on search filter
$approvals = [];
$status_filter = isset($_POST['status']) ? $_POST['status'] : "";

$sql = "SELECT approval_id, e_id, unit, department, from_date, to_date, no_of_days, reason, hod_dean_name, status FROM po_leave_approval";
if (!empty($status_filter)) {
    $sql .= " WHERE status = ?";
}

$stmt = $conn->prepare($sql);
if (!empty($status_filter)) {
    $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $approvals[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Approvals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Table Styling */
        .table-container {
            width: 100%;
            overflow-x: auto;
            max-height: 400px;
            border: 1px solid #ccc;
            padding: 5px;
            background-color: #f9f9f9;
            white-space: nowrap;
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
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
        <b style="font-size: 1.3rem">Admin Portal</b><br>
    </h1> 
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a class="active" href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_more.php"> More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            
            <li><a  href="manage_staff.php">View PO & Executive Account</a></li>
            <li><a class="active" href="po_leave.php">View PO leave</a></li> 
            <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
            
            
            </ul>
        </div>
        <div class="widget">

<div class="container">
    <h1 style="text-align: center;">Leave Approvals</h1>

    <!-- Search Form -->
    <form class="search_form" method="post">
        <label for="status">Filter by Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="PENDING" <?= ($status_filter == "PENDING") ? "selected" : "" ?>>Pending</option>
            <option value="APPROVED" <?= ($status_filter == "APPROVED") ? "selected" : "" ?>>Approved</option>
            <option value="REJECTED" <?= ($status_filter == "REJECTED") ? "selected" : "" ?>>Rejected</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <!-- Leave Approval Table -->
    <form method="post">
        <div class="table-container">
            <?php if (!empty($approvals)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>ID</th>
                            <th>Employee ID</th>
                            <th>Unit</th>
                            <th>Department</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>No. of Days</th>
                            <th>Reason</th>
                            <th>HOD/Dean</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvals as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_approvals[]" value="<?= $row['approval_id'] ?>"></td>
                                <td><?= htmlspecialchars($row['approval_id']) ?></td>
                                <td><?= htmlspecialchars($row['e_id']) ?></td>
                                <td><?= htmlspecialchars($row['unit']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['from_date']) ?></td>
                                <td><?= htmlspecialchars($row['to_date']) ?></td>
                                <td><?= htmlspecialchars($row['no_of_days']) ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= htmlspecialchars($row['hod_dean_name']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center;">No leave approvals found.</p>
            <?php endif; ?>
        </div>

        <!-- Update Status Form -->
        <div class="update_status_form">
            <label for="new_status">Change Status To:</label>
            <select name="new_status">
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
            </select>
            <button type="submit" name="update_status">Update Status</button>
        </div>
    </form>
</div>
            </div>
            </div>
<script src="script.js"></script>
</body>
</html>
