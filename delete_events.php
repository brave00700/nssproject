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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $eventId = intval($_POST['event_id']);

    // Check if the PDF exists in the database
    $stmt = $conn->prepare("SELECT event_id FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the PDF from the database
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        $deleteStmt->bind_param("i", $eventId);
        if ($deleteStmt->execute()) {
            echo "<script>alert('Event deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting the Event');</script>";
        }
        $deleteStmt->close();
    } else {
        echo "<script>alert('No Event found with the given ID.');</script>";
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminportal.css">
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
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
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a  href="manage_staff.php"> Manage Staff</a></li>
            <li><a  href="manage_announcements.php"> Announcements</a></li>
            <li><a class="active" href="manage_events.php"> Events</a></li>
            <li><a href="manage_inventory.php">Inventory</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
          <li><a  href="create_events.php">Create Events</a></li>
            <li><a  href="view_events.php">View Events</a></li>
            <li><a  href="modify_events.php">Modify Event Details</a></li>
            <li><a class="active" href="delete_events.php">Delete a event</a></li>
            
          </ul>
        </div>
        <div class="widget">
            <div class="delete">
                <h2>Delete a Event</h2>
                <form method="POST">
                    <label for="event_id">Enter PDF ID to Event:</label>
                    <input type="number" name="event_id" id="event_id" placeholder="Enter ID" required>
                    <button type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
