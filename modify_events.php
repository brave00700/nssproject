<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: login.html");
}            ?>
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_duration = $_POST['event_duration'];
    $event_type = $_POST['event_type'];
    $description = $_POST['description'];
    $teacher_incharge = $_POST['teacher_incharge'];
    $student_incharge = $_POST['student_incharge'];
    $event_venue = $_POST['event_venue'];
    $event_unit = isset($_POST['event_unit']) && $_POST['event_unit'] !== '' ? intval($_POST['event_unit']) : null;

    // Handle file uploads
    $posterPath = '';
    $budgetPdfPath = '';

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $posterPath = $uploadDir . basename($_FILES['poster']['name']);
        move_uploaded_file($_FILES['poster']['tmp_name'], $posterPath);
    }

    if (isset($_FILES['budget_pdf']) && $_FILES['budget_pdf']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $budgetPdfPath = $uploadDir . basename($_FILES['budget_pdf']['name']);
        move_uploaded_file($_FILES['budget_pdf']['tmp_name'], $budgetPdfPath);
    }

    // Build the SQL query dynamically
    $updates = [];
    if (!empty($event_name)) $updates[] = "event_name = '$event_name'";
    if (!empty($event_date)) $updates[] = "event_date = '$event_date'";
    if (!empty($event_time)) $updates[] = "event_time = '$event_time'";
    if (!empty($event_duration)) $updates[] = "event_duration = $event_duration";
    if (!empty($event_type)) $updates[] = "event_type = '$event_type'";
    if (!empty($description)) $updates[] = "description = '$description'";
    if (!empty($teacher_incharge)) $updates[] = "teacher_incharge = '$teacher_incharge'";
    if (!empty($student_incharge)) $updates[] = "student_incharge = '$student_incharge'";
    if (!empty($event_venue)) $updates[] = "event_venue = '$event_venue'";
    if (!is_null($event_unit)) $updates[] = "event_unit = $event_unit";
    if (!empty($posterPath)) $updates[] = "poster_path = '$posterPath'";
    if (!empty($budgetPdfPath)) $updates[] = "budget_pdf_path = '$budgetPdfPath'";

    if (count($updates) > 0) {
        $sql = "UPDATE events SET " . implode(", ", $updates) . " WHERE event_id = $event_id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Event details updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('No changes were made.');</script>";
    }
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminportal.css">

</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1><b style="font-size: 2.9rem;">National Service Scheme </b><br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
            <b style="font-size: 1.3rem">Admin Portal</b><br>
        </h1> 
        <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>

<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a href="view_admitted_students.php"> Manage Students</a></li>
            <li><a  href="view_po.php"> Manage Staff</a></li>
            <li><a  href="manage_announcements.php"> Announcements</a></li>
            <li><a class="active" href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
          <li><a  href="create_events.php">Create Events</a></li>
            <li><a href="view_events.php">View Events</a></li>
            <li><a class="active" href="modify_events.php">Modify Event Details</a></li>
            <li><a  href="delete_events.php">Delete a event</a></li>
           
          </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify Event Details</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="nss-form">
                <label for="event_id">Event ID:</label>
            <input type="number" id="event_id" name="event_id" required><br><br>

            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name"><br><br>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date"><br><br>

            <label for="event_time">Event Time:</label>
            <input type="time" id="event_time" name="event_time"><br><br>

            <label for="event_duration">Event Duration (hours):</label>
            <input type="number" id="event_duration" name="event_duration"><br><br>

            <label for="event_type">Event Type:</label>
        <select id="event_type" name="event_type" required >
          <option value="" disabled selected>Select </option>
          <option value="In-House">In-House</option>
          <option value="Out-House">Out-House</option>
        </select>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br><br>

            <label for="teacher_incharge">Teacher In-Charge:</label>
            <input type="text" id="teacher_incharge" name="teacher_incharge"><br><br>

            <label for="student_incharge">Student In-Charge:</label>
            <input type="text" id="student_incharge" name="student_incharge"><br><br>

            <label for="event_venue">Event Venue:</label>
            <input type="text" id="event_venue" name="event_venue"><br><br>

            <label for="event_unit">Unit:</label>
        <select id="event_unit" name="event_unit" required >
          <option value="" disabled selected>Select </option>
          <option value="1">1</option>
          <option value="2">2 </option>
          <option value="3">3 </option>
          <option value="4">4 </option>
          <option value="5">5 </option>
        </select>

            <label for="poster">Poster:</label>
            <input type="file" id="poster" name="poster"><br><br>

            <label for="budget_pdf">Budget PDF:</label>
            <input type="file" id="budget_pdf" name="budget_pdf"><br><br>

            
            
                    <div class="form-buttons">
                    <button type="submit">Update Event Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
