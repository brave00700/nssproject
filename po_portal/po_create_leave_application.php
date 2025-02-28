<?php
session_start();

// Check if the session contains a valid PO login and unit
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Get logged-in PO details
$po_id = $_SESSION['po_id'];
$po_unit = $_SESSION['unit'];
$e_id = $_SESSION['user_id']; // Assuming 'user_id' is stored in session for staff identification

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];
    $hod_dean_name = $_POST['hod_dean_name'];
    $department = $_POST['department'];

   // Validate Dates
$fromDateTime = strtotime($from_date);
$toDateTime = strtotime($to_date);
$currentDateTime = strtotime(date("Y-m-d")); // Get today's date

if ($fromDateTime === false || $toDateTime === false) {
    echo "<script>alert('Invalid date format. Please select valid dates.'); window.location.href = 'po_create_leave_application.php';</script>";
    exit();
}

if ($fromDateTime < $currentDateTime) {
    echo "<script>alert('Error: From Date cannot be before today.'); window.location.href = 'po_create_leave_application.php';</script>";
    exit();
}

if ($fromDateTime > $toDateTime) {
    echo "<script>alert('Error: From Date cannot be after To Date.'); window.location.href = 'po_create_leave_application.php';</script>";
    exit();
}


    // Calculate number of days
    $no_of_days = ceil(($toDateTime - $fromDateTime) / (60 * 60 * 24)) + 1;

    // Insert into database
    $sql = "INSERT INTO po_leave_approval (e_id, unit, department, from_date, to_date, no_of_days, reason, hod_dean_name, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssiss", $e_id, $po_unit, $department, $from_date, $to_date, $no_of_days, $reason, $hod_dean_name);
    
    if ($stmt->execute()) {
        echo "<script>alert('Leave application submitted successfully!'); window.location.href = 'po_view_leave_application.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'po_view_leave_application.php';</script>";
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
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b><br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
        <b style="font-size: 1.3rem">Program Officer Portal</b><br>
    </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a   href="po_manage_application.php">Manage Applications</a></li>
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
            <li><a href="po_view_events.php"> View Events</a></li>
            <li><a class="active" href="po_view_leave_application.php"> View Leave Application</a></li>
            <li><a   href="po_view_grievance.php">View Grievance</a></li>

            </ul>
        </div>

    
        <div class="widget">
        <div class="mainapply">
    <h2>Apply for Leave</h2>
    <form action="" method="post" class="nss-form">
        <label for="from_date">From Date:</label>
        <input type="date" id="from_date" name="from_date" required>

        <label for="to_date">To Date:</label>
        <input type="date" id="to_date" name="to_date" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" required></textarea>

        <label for="hod_dean_name">HOD/Dean Name:</label>
        <input type="text" id="hod_dean_name" name="hod_dean_name" required>

        <div class="form-buttons">
            <button type="submit">Submit Leave Request</button>
        </div>
    </form>
</div>
</div>
</div>
<script src="script.js"></script>
</body>
</html>
