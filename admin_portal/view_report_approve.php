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

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

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
    $new_status = $_POST['status'];

    $updateQuery = "UPDATE credits SET status = ? WHERE credit_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $new_status, $credit_id);
    
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/view_report_approve.css">

    <style>/* General Styles */

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
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                        <li><a  href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>
<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            
            <ul>
                <li><a href="manage_students.php">Admitted Students</a></li>
                <li><a href="admin_approve_attendance.php">Approve Attendance</a></li>

                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a class="active"  href="view_credit_application.php">Credits Application</a></li>
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
        <select name="credits" id="credits" required disabled>
            <option value="0" <?= ($creditData['credits'] == '0') ? 'selected' : '' ?>>0</option>
            <option value="1" <?= ($creditData['credits'] == '1') ? 'selected' : '' ?>>1</option>
            <option value="2" <?= ($creditData['credits'] == '2') ? 'selected' : '' ?>>2</option>
            <option value="3" <?= ($creditData['credits'] == '3') ? 'selected' : '' ?>>3</option>
        </select>
        
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="PENDING" <?= ($creditData['status'] == 'PENDING') ? 'selected' : '' ?>>Pending</option>
            <option value="APPROVED" <?= ($creditData['status'] == 'APPROVED') ? 'selected' : '' ?>>Approved</option>
            
        </select>
        
        <button name="update" type="submit">Update</button>
    </form>
    <br>
    <button onclick="window.location.href='view_credit_application.php'">Back to Manage Credits</button>
<script src="script.js"></script>
            </div></div></div></div>
</body>
</html>
