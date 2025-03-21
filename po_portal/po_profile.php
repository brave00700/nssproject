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
            <li><a  class="active" href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a  href="po_manage_reports.php">Reports & Registers</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
        
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="po_profile.php">View Profile</a></li>
            <li><a href="po_pass_change.php">Change Password</a></li>
            
          </ul>
        </div>
        <div class="widget">
            <?php
          
            $po_id = $_SESSION['po_id'];
            // Create a connection object
            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn->prepare("SELECT  name, phone, email,dob, gender, address,role, user_id, profile_photo, unit FROM staff WHERE user_id = ?");
            $stmt->bind_param("s", $po_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photoPath = $row['profile_photo']; // Path to the photo
                echo "<table>
                        <tr>
                            <td>Profile</td>
                           <td><img src=\"..$photoPath\" style=\"width: 50px; height: 50px;\"></td>

                        </tr>
                        
                        <tr>
                            <td>Name</td>
                            <td>{$row['name']}</td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                            <td>{$row['unit']}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>{$row['role']}</td>
                        </tr>
                        <tr>
                            <td>User Id</td>
                            <td>{$row['user_id']}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{$row['phone']}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{$row['email']}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{$row['address']}</td>
                        </tr>
                        
                        <tr>
                            <td>Gender</td>
                            <td>{$row['gender']}</td>
                        </tr>
                        
                    </table>";
            }else {
                echo "User Not Found";
                header("Location: ../login.html");
            }
            ?>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
