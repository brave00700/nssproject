<?php
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
    <link rel="stylesheet" href="../style.css">
   
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Program Officer Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
        
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="po_profile.php">View Profile</a></li>
            <li><a href="">Change Password</a></li>
            
          </ul>
        </div>
        <div class="widget">
            <?php
          
            $po_id = $_SESSION['po_id'];
            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "nss_db");
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
                           <td><img src=\"./$photoPath\" style=\"width: 50px; height: 50px;\"></td>

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
