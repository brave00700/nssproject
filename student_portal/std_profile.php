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
            <b style="font-size: 1.3rem">Student Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="std_profile.php">Profile</a></li>
            <li><a href="std_attendance.php">Attendance</a></li>
            <li><a  href="std_events.php">Events</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="std_profile.php">View Profile</a></li>
            <li><a href="std_pass_change.php">Change Password</a></li>
            <li><a href="std_logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="widget">
            <?php
            //Starting a session
            session_start();

            //Storing session user
            $reg = $_SESSION['reg'];

            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "nss_application");
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn->prepare("SELECT Register_no, Name, Father_name, Mother_name, Phone, Email, Age, Gender, Category, Bloodgroup, Shift, Course, ProfilePhoto, Unit FROM admitted_students WHERE user_id = ?");
            $stmt->bind_param("s", $reg);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photoPath = $row['ProfilePhoto']; // Path to the photo
                echo "<table>
                        <tr>
                            <td>Profile</td>
                            <td><img src=\"../$photoPath\" style=\"width: 50px; height: 50px;\"></td>
                        </tr>
                        <tr>
                            <td>Register No</td>
                            <td>{$row['Register_no']}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{$row['Name']}</td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                            <td>{$row['Unit']}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{$row['Phone']}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{$row['Email']}</td>
                        </tr>
                    </table>";
            }else {
                echo "User Not Found";
                header("Location: std_login.php");
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>