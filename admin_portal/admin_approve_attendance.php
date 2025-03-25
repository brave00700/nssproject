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


// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])){
    $_SESSION['att_evt_id'] = $_POST['event_id'];
    header("Location: admin_approve_confirm.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/manage_student.css">

    <style>
    /* Table styling */


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
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                                    <li><a href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>



<div class="main">
<div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a   href="manage_students.php">Admitted Students</a></li>
                <li><a class="active" href="admin_approve_attendance.php">Approve Attendance</a></li>
                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a  href="view_credit_application.php">Credits Application</a></li>
                
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
</div>
        <div class="widget">
            <table>
            <?php
          
           
            // Create a connection object
            $conn_event = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if($conn_event->connect_error){
                die("Connection failed: " . $conn_event->connect_error);
            }
            $stmt_event = $conn_event->prepare("SELECT event_name, event_date, event_id FROM events");
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

