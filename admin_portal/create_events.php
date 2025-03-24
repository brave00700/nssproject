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
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}


// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_duration = $_POST['event_duration'];
    $event_type = $_POST['event_type'];
    $description = $_POST['description'];
    $teacher_incharge = $_POST['teacher_incharge'];
    $student_incharge = $_POST['student_incharge'];
    $event_venue = $_POST['event_venue'];
    $event_unit = $_POST['event_unit'];
    
    $poster_path = null;
    $uploadDir = '../assets/uploads/event_posters/';

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle poster upload
    if (!empty($_FILES['poster']['name']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $posterTmpPath = $_FILES['poster']['tmp_name'];
        $posterName = basename($_FILES['poster']['name']);
        $posterSize = $_FILES['poster']['size'];
        $posterType = mime_content_type($posterTmpPath);
        $allowedPosterTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $maxPosterSize = 2 * 1024 * 1024; // 2MB

        if ($posterSize <= $maxPosterSize && in_array($posterType, $allowedPosterTypes)) {
            $filePath = $uploadDir . time() . "_" . $posterName; // Prevent filename conflicts
            if (move_uploaded_file($posterTmpPath, $filePath)) {
                $poster_path = str_replace('..', '', $filePath);
            } else {
                echo "<script>alert('Error: Failed to upload the poster.'); window.location.href = 'view_events.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Error: Invalid poster file type or size exceeds 2MB.'); window.location.href = 'view_events.php';</script>";
            exit;
        }
    }

    // Prepare SQL statement
    $sql = "INSERT INTO events 
            (event_name, event_date, event_time, event_duration, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue, event_unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssssss", 
        $event_name, $event_date, $event_time, $event_duration, $poster_path, $event_type, 
        $description, $teacher_incharge, $student_incharge, $event_venue, $event_unit
    );

    if ($stmt->execute()) {
        echo "<script>alert('Event created successfully!'); window.location.href = 'view_events.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'view_events.php';</script>";
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
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                        <li><a class="active" href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active"  href="view_events.php">Events</a></li>
            <li><a  href="view_grievances.php">Grievances</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>

            <li><a href="manage_images.php">Upload Images to gallery</a></li>
            
            
          </ul>
        </div>

        <div class="widget">
    <div class="mainapply">
      <h2>Create event</h2>
      <form action="" method="post" class="nss-form" enctype="multipart/form-data" onsubmit="return validateForm();">
      <label for="event_name">Event Name:</label>
    <input type="text" id="event_name" name="event_name" required>

    <label for="event_date">Event Date:</label>
    <input type="date" id="event_date" name="event_date" required>

    <label for="event_time">Event Time:</label>
    <input type="time" id="event_time" name="event_time" required>

    <label for="event_duration">Event Duration (hours):</label>
    <input type="number" id="event_duration" name="event_duration" required>

    
    <label for="event_type">Event Type:</label>
        <select id="event_type" name="event_type" required >
          <option value="" disabled selected>Select </option>
          <option value="In-House">In-House</option>
          <option value="Out-House">Out-House</option>
        </select>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>

    <label for="teacher_incharge">Teacher In-charge:</label>
    <input type="text" id="teacher_incharge" name="teacher_incharge" required>

    <label for="student_incharge">Student In-charge:</label>
    <input type="text" id="student_incharge" name="student_incharge" required>

    <label for="event_venue">Event Venue:</label>
    <input type="text" id="event_venue" name="event_venue" required>

    <label for="event_unit">Unit:</label>
        <select id="event_unit" name="event_unit" required >
          <option value="" disabled selected>Select </option>
          <option value="1">1</option>
          <option value="2">2 </option>
          <option value="3">3 </option>
          <option value="4">4 </option>
          <option value="5">5 </option>
          <option value="All">All </option>
        </select>

    <label for="poster">Event Poster (JPEG, PNG, JPG, WEBP max size: 2MB):</label>
    <input type="file" id="poster" name="poster" accept="image/jpeg, image/png, image/jpg, image/webp" required>

    

    

        <div class="form-buttons">
        <button type="submit">Create Event</button>
        </div>
      </form>
      </div>
    </div>
</div>
<script>
        function validateForm() {
    let eventName = document.getElementById("event_name")?.value.trim();
    let eventDesc = document.getElementById("description")?.value.trim();
    let eventDate = document.getElementById("event_date")?.value;
    let eventTime = document.getElementById("event_time")?.value;
    let eventDuration = document.getElementById("event_duration")?.value;
    let eventVenue = document.getElementById("event_venue")?.value.trim();
    let teacherIncharge = document.getElementById("teacher_incharge")?.value.trim();
    let studentIncharge = document.getElementById("student_incharge")?.value.trim();

    let today = new Date();
    let selectedDate = new Date(eventDate);
    let selectedTime = eventTime ? parseInt(eventTime.split(":")[0]) : null;

    // Validate Event Name (Required)
    if (!eventName) {
        alert("Event Name is required.");
        return false;
    }

    // Validate Event Description (Required)
    if (!eventDesc) {
        alert("Event Description is required.");
        return false;
    }

    // Validate Event Date (Should be after today)
    if (!eventDate) {
        alert("Event Date is required.");
        return false;
    }
    if (selectedDate <= today) {
        alert("Event Date must be after today's date.");
        return false;
    }

    // Validate Event Time (Between 6 AM and 10 PM)
    if (!eventTime) {
        alert("Event Time is required.");
        return false;
    }
    if (selectedTime < 6 || selectedTime >= 22) {
        alert("Event Time must be between 6 AM and 10 PM.");
        return false;
    }

    // Validate Event Duration (Must be positive)
    if (!eventDuration || eventDuration <= 0) {
        alert("Event Duration must be a positive number.");
        return false;
    }

    // Validate Event Venue (Required)
    if (!eventVenue) {
        alert("Event Venue is required.");
        return false;
    }

    // Validate Teacher In-Charge (Required)
    if (!teacherIncharge) {
        alert("Teacher In-Charge is required.");
        return false;
    }

    // Validate Student In-Charge (Required)
    if (!studentIncharge) {
        alert("Student In-Charge is required.");
        return false;
    }

    return true; // ✅ Form is valid, allow submission
}
    </script>
<script src="script.js"></script>
</body>
</html>