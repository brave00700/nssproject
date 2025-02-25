<?php
session_start();

// Redirect to login if not authenticated
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

// Ensure a credit ID is selected
if (!isset($_POST['credit_id'])) {
    header("Location: view_credit_application.php");
    exit();
}

$credit_id = intval($_POST['credit_id'][0]);

// Fetch credit application details
$creditQuery = "SELECT * FROM credits WHERE credit_id = ?";
$stmt = $conn->prepare($creditQuery);
$stmt->bind_param("i", $credit_id);
$stmt->execute();
$creditResult = $stmt->get_result();
$creditData = $creditResult->fetch_assoc();
$stmt->close();

if (!$creditData) {
    header("Location: view_credit_application.php");
    exit();
}

$register_no = $creditData['register_no'];

// Fetch student details and events
$query = "
    SELECT s.user_id AS reg_no, s.name, e.event_name, e.event_date, e.event_duration 
    FROM attendance a
    JOIN events e ON a.event_id = e.event_id
    JOIN students s ON a.register_no = s.user_id
    WHERE a.register_no = ? AND a.status = 'APPROVED'
    ORDER BY e.event_date ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $register_no);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
$studentName = "N/A";
$totalDuration = 0;

if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $studentName = $rows[0]['name'];
    $totalDuration = array_sum(array_column($rows, 'event_duration'));
    $reports = $rows;
}
$stmt->close();

// Handle update request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $new_credits = $_POST['credits'];
    $new_status = $_POST['status'];

    $updateQuery = "UPDATE credits SET credits = ?, status = ? WHERE credit_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $new_credits, $new_status, $credit_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Credit details updated successfully.'); window.location.href='view_credit_application.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Credit Application</title>
    <link rel="stylesheet" href="../style.css">
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
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a  href="manage_announcements.php"> Announcements</a></li>
            <li><a  href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>
<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            
            <ul>
                <li><a href="manage_students.php">View Admitted Students</a></li>
                <li><a class="active"  href="view_credit_application.php">View Credits Application</a></li>
                <li><a href="change_student_password.php">Change Student Password</a></li>
            
            
            </ul>
        </div>
        <div class="widget">
<h1 style="text-align:center;">Student Reports</h1>
    <p><strong>Register Number:</strong> <?= htmlspecialchars($register_no) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($studentName) ?></p>
    <table border="1">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Duration (hrs)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reports)): ?>
                <?php foreach ($reports as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['event_name']) ?></td>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td><?= htmlspecialchars($event['event_duration']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align: center;">Total Duration:</td>
                    <td style="font-weight: bold; text-align: center;"> <?= htmlspecialchars($totalDuration) ?> hrs</td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">No events found for this student.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Modify Credit Details</h2>
    <form method="POST">
        <input type="hidden" name="credit_id" value="<?= htmlspecialchars($credit_id) ?>">
        
        <label for="credits">Credits:</label>
        <select name="credits" id="credits" required>
            <option value="0" <?= ($creditData['credits'] == '0') ? 'selected' : '' ?>>0</option>
            <option value="1" <?= ($creditData['credits'] == '1') ? 'selected' : '' ?>>1</option>
            <option value="2" <?= ($creditData['credits'] == '2') ? 'selected' : '' ?>>2</option>
            <option value="3" <?= ($creditData['credits'] == '3') ? 'selected' : '' ?>>3</option>
        </select>
        
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="PENDING" <?= ($creditData['status'] == 'PENDING') ? 'selected' : '' ?>>Pending</option>
            <option value="APPROVED" <?= ($creditData['status'] == 'APPROVED') ? 'selected' : '' ?>>Approved</option>
            <option value="PO_APPROVED" <?= ($creditData['status'] == 'PO_APPROVED') ? 'selected' : '' ?>>PO Approved</option>
            <option value="REJECTED" <?= ($creditData['status'] == 'REJECTED') ? 'selected' : '' ?>>Rejected</option>
        </select>
        
        <button name="update" type="submit">Update</button>
    </form>
    <br>
    <button onclick="window.location.href='view_credit_application.php'">Back to Manage Credits</button>
<script src="script.js"></script>
            </div></div></div></div>
</body>
</html>
