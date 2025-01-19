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
$dbname = "attendance_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$reports = [];
$selectedStudents = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_no'])) {
    // Sanitize and process the register numbers
    $registerNos = $_POST['register_no'];
    
    foreach ($registerNos as $regNo) {
        $regNo = $conn->real_escape_string($regNo);

        // Query to get student report
        $query = "
            SELECT reg_no, name, event_name, event_date, duration_hrs
            FROM attendance
            WHERE reg_no = '$regNo' AND status = 'Approved'
            ORDER BY event_date ASC
        ";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            // Extract the first row for name and register number
            $studentName = $rows[0]['name'];
            $studentRegNo = $rows[0]['reg_no'];

            // Calculate total duration
            $totalDuration = array_sum(array_column($rows, 'duration_hrs'));

            $reports[] = [
                'name' => $studentName,
                'reg_no' => $studentRegNo,
                'events' => $rows,
                'total_duration' => $totalDuration,
            ];
        } else {
            // Handle no data case
            $reports[] = [
                'name' => 'N/A',
                'reg_no' => $regNo,
                'events' => [],
                'total_duration' => 0,
            ];
        }

        // Add to selected students
        $selectedStudents[] = $regNo;
    }
} else {
    header("Location: view_admitted_students.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
   
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
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
            <li><a class="active"  href="view_admitted_students.php"> Manage Students</a></li>
            <li><a href="view_po.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
           
            
            <li><a href="view_admitted_students.php">View Admitted Students</a></li>
            
            <li><a href="change_student_password.php">Change Student Password</a></li>
            
            
          </ul>
        </div>
        <div class="widget">
    <h1>Student Reports</h1>
    <?php if (!empty($reports)): ?>
        <?php foreach ($reports as $report): ?>
            <div class="report-container">
                <p><strong>Register Number:</strong> <?= htmlspecialchars($report['reg_no']) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($report['name']) ?></p>
                <table border="1">
                    <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Duration (hrs)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($report['events'])): ?>
                        <?php foreach ($report['events'] as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['event_name']) ?></td>
                                <td><?= htmlspecialchars($event['event_date']) ?></td>
                                <td><?= htmlspecialchars($event['duration_hrs']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2" style="font-weight: bold; text-align: center;">Total Duration:</td>
                            <td style="font-weight: bold; text-align: center;"><?= htmlspecialchars($report['total_duration']) ?> hrs</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No events found for this student.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <br>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No students selected.</p>
    <?php endif; ?>
    <button onclick="window.location.href='view_admitted_students.php'" class="admit-buttons">Back to Manage Students</button>
    </div>
    </div>
</div>
</body>
</html>