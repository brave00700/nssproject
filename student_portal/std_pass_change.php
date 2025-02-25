<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
    <style>
        input, .label {
            font-size: 1.1rem;
        }
        .label {
            text-align: right;
        }
        input {
            width: 200px;
        }
        button {
            background-color: #FFA200;
            border: none;
            color: #FFFFFF;
            padding: 0.5rem;
            font-weight: 700;
            width: 200px;
        }
        button:active {
            background-color: #e69202;
        }
    </style>
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
            <li><a href="std_attendance_view.php">Attendance</a></li>
            <li><a  href="std_events.php">Events</a></li>
            <li><a  href="std_griev.php">Grievience</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="std_profile.php">View Profile</a></li>
            <li><a class="active" href="std_pass_change.php">Change Password</a></li>
            <li><a href="std_logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="widget">
            <form method="post">
            <table>
                    <tr>
                        <td class="label">Old Password</td>
                        <td><input type="text" name='old_pass' required></td>
                    </tr>
                    <tr>
                        <td class="label">New Password</td>
                        <td><input type="password" name='new_pass' required></td>
                    </tr>
                    <tr>
                        <td class="label">Confirm Password</td>
                        <td><input type="password" name='confirm_pass' required></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button name="change" type="submit">Login</button></td>
                    </tr>
            </table>
            </form>
            <?php
            // Creating a new session
            session_start();

            //Checking user session timeout
            if(isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']){
                session_unset();
                session_destroy();
                header("Location: std_login.php");
                exit();
            }
            //Update last activity time
            $_SESSION['last_seen'] = time();

            // Storing session variable
            if(!$_SESSION['reg']){
                header("Location: std_login.php");
            }
            $reg = $_SESSION['reg'];
            
            // Checking for change password
            if(isset($_POST['change'])){
                $old_pass = $_POST['old_pass'];
                $new_pass = $_POST['new_pass'];
                $confirm_pass = $_POST['confirm_pass'];

                if($new_pass != $confirm_pass){
                    echo 'New Passwords do not match';
                }else{
                    // Create a connection object
                    $conn = new mysqli("localhost", "root", "", "nss_application");
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt1 = $conn->prepare("SELECT user_id, password FROM admitted_students WHERE user_id = ?");
                    $stmt1->bind_param("s", $reg);
                    $stmt1->execute();
                    $result = $stmt1->get_result();

                    if($result->num_rows > 0) {
                        $cred = $result->fetch_assoc();
                        if($cred['password'] != $old_pass){
                            echo 'Incorrect Password';
                        }else{
                            $stmt2 = $conn->prepare("UPDATE admitted_students SET password = ? WHERE user_id = ?");
                            $stmt2->bind_param("ss",$new_pass,$cred['user_id']);
                            if($stmt2->execute()){
                                echo 'Password Updated Successfully';
                            }else {
                                echo 'Error updating email: ' . $stmtUpdate->error;
                            }
                        }
                    }else{
                        echo 'No User Found';
                        header("Location: std_login.php");
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
