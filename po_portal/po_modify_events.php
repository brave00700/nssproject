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
   

<<<<<<< Updated upstream
=======

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_db";
>>>>>>> Stashed changes


$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$event = [];
$event_id = null;

// Auto-fetch event details when event_id is entered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    
    $sql = "SELECT * FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "<script>alert('No event found with the given ID.');</script>";
    }
    $stmt->close();
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_details'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_desc = $_POST['event_desc'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_duration = $_POST['event_duration'];
    $event_type = $_POST['event_type'];
    $event_venue = $_POST['event_venue'];
    $teacher_incharge = $_POST['teacher_incharge'];
    $student_incharge = $_POST['student_incharge'];
    $event_unit = $_POST['event_unit'];

   // Handle file uploads
$posterPath = $event['poster_path'] ?? '';


if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../assets/uploads/event_posters/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = basename($_FILES['poster']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['poster']['tmp_name'], $filePath)) {
        // Store path in database without '../'
        $posterPath = 'assets/uploads/event_posters/' . $fileName;
    }
}




    $sql = "UPDATE events SET event_name = ?, event_desc = ?, event_date = ?, event_time = ?, 
            event_duration = ?, event_type = ?, event_venue = ?, teacher_incharge = ?, 
            student_incharge = ?, event_unit = ?, poster_path = ?
            WHERE event_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissssssi", $event_name, $event_desc, $event_date, $event_time, 
                      $event_duration, $event_type, $event_venue, $teacher_incharge, 
                      $student_incharge, $event_unit, $posterPath, $event_id);

    
    if ($stmt->execute()) {
        echo "<script>alert('Event details updated successfully!'); window.location.href = 'po_view_events.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "'); window.location.href = 'po_view_events.php';</script>";
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../adminportal.css">

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
            <li><a class="active" href="po_view_events.php"> View Events</a></li>
            <li><a  href="po_view_leave_application.php"> View Leave Application</a></li>
            <li><a   href="po_view_grievance.php">View Grievance</a></li>

            </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify Event Details</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="nss-form">
            <label for="event_id">Event ID:</label>
            <input type="number" id="event_id" name="event_id" value="<?= $event['event_id'] ?? '' ?>" required readonly><br><br>

            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" value="<?= $event['event_name'] ?? '' ?>"><br><br>

            <label for="event_desc">Event Description:</label>
            <textarea id="event_desc" name="event_desc"><?= $event['event_desc'] ?? '' ?></textarea><br><br>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date" value="<?= $event['event_date'] ?? '' ?>"><br><br>

            <label for="event_time">Event Time:</label>
            <input type="time" id="event_time" name="event_time" value="<?= $event['event_time'] ?? '' ?>"><br><br>

            <label for="event_duration">Event Duration (hours):</label>
            <input type="number" id="event_duration" name="event_duration" value="<?= $event['event_duration'] ?? '' ?>"><br><br>

            <label for="event_type">Event Type:</label>
            <select id="event_type" name="event_type">
                <option value="IN-HOUSE" <?= ($event['event_type'] ?? '') === 'IN-HOUSE' ? 'selected' : '' ?>>IN-HOUSE</option>
                <option value="OUT-HOUSE" <?= ($event['event_type'] ?? '') === 'OUT-HOUSE' ? 'selected' : '' ?>>OUT-HOUSE</option>
            </select><br><br>

            <label for="event_venue">Event Venue:</label>
            <input type="text" id="event_venue" name="event_venue" value="<?= $event['event_venue'] ?? '' ?>"><br><br>

            <label for="teacher_incharge">Teacher In-Charge:</label>
            <input type="text" id="teacher_incharge" name="teacher_incharge" value="<?= $event['teacher_incharge'] ?? '' ?>"><br><br>

            <label for="student_incharge">Student In-Charge:</label>
            <input type="text" id="student_incharge" name="student_incharge" value="<?= $event['student_incharge'] ?? '' ?>"><br><br>

            <label for="event_unit">Event Unit:</label>
            <select id="event_unit" name="event_unit" disabled>
                <option value="1" <?= ($event['event_unit'] ?? '') === '1' ? 'selected' : '' ?>>1</option>
                <option value="2" <?= ($event['event_unit'] ?? '') === '2' ? 'selected' : '' ?>>2</option>
                <option value="3" <?= ($event['event_unit'] ?? '') === '3' ? 'selected' : '' ?>>3</option>
                <option value="4" <?= ($event['event_unit'] ?? '') === '4' ? 'selected' : '' ?>>4</option>
                <option value="5" <?= ($event['event_unit'] ?? '') === '5' ? 'selected' : '' ?>>5</option>
                <option value="All" <?= ($event['event_unit'] ?? '') === 'All' ? 'selected' : '' ?>>All</option>
            </select><br><br>

            <label for="poster">Poster:</label>
            <input type="file" id="poster" name="poster"><br><br>

            <label for="budget_pdf">Budget PDF:</label>
            <input type="file" id="budget_pdf" name="budget_pdf"><br><br>

            <button type="submit" name="update_details">Update Event Details</button>
        </form>
    </div>
<script src="script.js"></script>
</body>
</html>
