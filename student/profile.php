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
<div class="logo-container">
        <img class="sjulogo" src="../assets/icons/sju_logo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Student Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../assets/icons/nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a  href="events.php">Events</a></li>
            <li><a  href="griev.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="profile.php">View Profile</a></li>
            <li><a href="pass_change.php">Change Password</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="widget">
            <?php
            //Starting a session
            session_start();

            //Checking user session timeout
            if(isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']){
                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            }
            //Update last activity time
            $_SESSION['last_seen'] = time();

            // Storing session variable
            if(!$_SESSION['reg']){
                header("Location: login.php");
            }
            $reg = $_SESSION['reg'];

            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "nss_db");
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn->prepare("SELECT register_no, name, father_name, mother_name, phone, email, age, gender, category, bloodgroup, shift, course, profile_photo, unit, address FROM students WHERE user_id = ?");
            $stmt->bind_param("s", $reg);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photoPath = $row['profile_photo']; // Path to the photo
                echo "<table>
                        <tr>
                            <td>Profile</td>
                            <td><img src=\"../$photoPath\" style=\"width: 50px; height: 50px;\"></td>
                        </tr>
                        <tr>
                            <td>Register No</td>
                            <td>{$row['register_no']}</td>
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
                            <td>Shift</td>
                            <td>{$row['shift']}</td>
                        </tr>
                        <tr>
                            <td>Course</td>
                            <td>{$row['course']}</td>
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
                            <td>Father Name</td>
                            <td>{$row['father_name']}</td>
                        </tr>
                        <tr>
                            <td>Mother Name</td>
                            <td>{$row['mother_name']}</td>
                        </tr>
                        <tr>
                            <td>Age</td>
                            <td>{$row['age']}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{$row['gender']}</td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>{$row['category']}</td>
                        </tr>
                        <tr>
                            <td>Bloodgroup</td>
                            <td>{$row['bloodgroup']}</td>
                        </tr>
                    </table>";
            }else {
                echo "User Not Found";
                header("Location: login.php");
            }
            ?>
        </div>
    </div>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
