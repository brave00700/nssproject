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
            <li><a class="active" href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="po_approve_attendance.php">View Attendance</a></li>
            <li><a href="">###</a></li>
            <li><a href="">###</a></li>
          </ul>
        </div>
        <div class="widget">
            <table>
            <?php
          
            $po_id = $_SESSION['po_id'];
            $po_unit = $_SESSION['unit'];
            // Create a connection object
            $conn_event = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if($conn_event->connect_error){
                die("Connection failed: " . $conn_event->connect_error);
            }
            $stmt_event = $conn_event->prepare("SELECT event_name, event_date, event_id FROM events WHERE event_unit = ? OR event_unit = 'All'");
            $stmt_event->bind_param("s", $po_unit);
            $stmt_event->execute();
            $result = $stmt_event->get_result();

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                echo "<tr>
                <td>{$row['event_name']}</td>
                <td>{$row['event_date']}</td>
                <td><form method='POST'>
                    <input type='hidden' name='event_id' value='{$row['event_id']}'>
                    <input type='submit' value='Approve'>
                </form></td></tr>";
                }
            }
            $stmt_event->close();
            $conn_event->close();
            ?>
            </table>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])){
    $_SESSION['att_evt_id'] = $_POST['event_id'];
    header("Location: po_approve_confirm.php");
}
?>