<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}            ?>
<?php
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

// Collect form data
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
    $budget_pdf_path = null;

    // Handle poster upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $posterTmpPath = $_FILES['poster']['tmp_name'];
        $posterName = $_FILES['poster']['name'];
        $posterSize = $_FILES['poster']['size'];
        $posterType = mime_content_type($posterTmpPath);
        $allowedPosterTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxPosterSize = 2 * 1024 * 1024; // 2MB

        if ($posterSize > $maxPosterSize || !in_array($posterType, $allowedPosterTypes)) {
            echo "<script>alert('Error: Invalid poster file type or size exceeds 2MB.'); window.location.href = 'view_events.php';</script>";
            exit;
        }

        $posterDir = '../uploads/event_posters/';
        if (!is_dir($posterDir)) {
            mkdir($posterDir, 0777, true);
        }

        $poster_path = $posterDir . basename($posterName);
        if (!move_uploaded_file($posterTmpPath, $poster_path)) {
            echo "<script>alert('Error: Failed to upload the poster. Please try again.'); window.location.href = 'view_events.php';</script>";
            exit;
        }
    }

    // Handle budget PDF upload
    if (isset($_FILES['budget_pdf']) && $_FILES['budget_pdf']['error'] === UPLOAD_ERR_OK) {
        $budgetTmpPath = $_FILES['budget_pdf']['tmp_name'];
        $budgetName = $_FILES['budget_pdf']['name'];
        $budgetSize = $_FILES['budget_pdf']['size'];
        $budgetType = mime_content_type($budgetTmpPath);
        $allowedBudgetTypes = ['application/pdf'];
        $maxBudgetSize = 2 * 1024 * 1024; // 2MB

        if ($budgetSize > $maxBudgetSize || !in_array($budgetType, $allowedBudgetTypes)) {
            echo "<script>alert('Error: Invalid PDF file type or size exceeds 2MB.'); window.location.href = 'view_events.php';</script>";
            exit;
        }

        $budgetDir = '../uploads/budget_pdfs/';
        if (!is_dir($budgetDir)) {
            mkdir($budgetDir, 0777, true);
        }

        $budget_pdf_path = $budgetDir . basename($budgetName);
        if (!move_uploaded_file($budgetTmpPath, $budget_pdf_path)) {
            echo "<script>alert('Error: Failed to upload the budget PDF. Please try again.'); window.location.href = 'view_events.php';</script>";
            exit;
        }
    }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO events 
            (event_name, event_date, event_time, event_duration, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue, budget_pdf_path, event_unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssissssssss",
        $event_name,
        $event_date,
        $event_time,
        $event_duration,
        $poster_path,
        $event_type,
        $description,
        $teacher_incharge,
        $student_incharge,
        $event_venue,
        $budget_pdf_path,
        $event_unit
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
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
                        <li><a class="active" href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active"  href="view_events.php">View Events</a></li>
            <li><a  href="view_grievances.php">View Grievances</a></li>
            
            
          </ul>
        </div>

        <div class="widget">
    <div class="mainapply">
      <h2>Create event</h2>
      <form action="" method="post" class="nss-form" enctype="multipart/form-data">
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

    <label for="poster">Event Poster (JPEG, PNG, JPG, max size: 2MB):</label>
    <input type="file" id="poster" name="poster" accept="image/jpeg, image/png, image/jpg" required>

    <label for="budget_pdf">Budget PDF (PDF, max size: 5MB):</label>
    <input type="file" id="budget_pdf" name="budget_pdf" accept="application/pdf" required>

    

        <div class="form-buttons">
        <button type="submit">Create Event</button>
        </div>
      </form>
      </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
+
