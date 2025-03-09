<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .widget {
    width: 100%;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.widget form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.widget .label {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    text-align: left;
    width: 100%;
}

.widget input {
    width: 100%;
    max-width: 300px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    transition: border 0.3s ease-in-out;
}

.widget input:focus {
    border-color: #007bff;
    outline: none;
}

.widget button {
    background-color: #FFA200;
    border: none;
    color: #fff;
    padding: 10px;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 6px;
    width: 100%;
    max-width: 300px;
    cursor: pointer;
    transition: background 0.3s ease-in-out;
}

.widget button:hover {
    background-color: #e69202;
}

/* Responsive Design */
@media (max-width: 600px) {
    .widget {
        width: 90%;
        padding: 20px;
    }

    .widget input {
        max-width: 100%;
    }

    .widget button {
        max-width: 100%;
    }
    
}
p.msg {
        width: 350px;
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        background-color: #ffb1005c; 
        color:rgb(255, 0, 0);
        font-weight: 700;
        padding: 1rem;   
        text-align: center;
        margin: 20px auto;
    }

    </style>
</head>
<body>
<?php include "header.php" ?>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a  href="events.php">Events</a></li>
            <li><a  href="grievance.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="update_profile.php">Request Profile Update</a></li>
            <li><a class="active" href="pass_change.php">Change Password</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="widget">
            <form method="post">
            <div class="input-group">
            <label class="label">Old Password</label>
            <input type="password" name="old_pass" required>
        </div>
        <div class="input-group">
            <label class="label">New Password</label>
            <input type="password" name="new_pass" required>
        </div>
        <div class="input-group">
            <label class="label">Confirm Password</label>
            <input type="password" name="confirm_pass" required>
        </div>
        <button name="change" type="submit">Change Password</button>
            </form>
            <?php
            // Creating a new session
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
            
            // Checking for change password
            if(isset($_POST['change'])){
                $old_pass = $_POST['old_pass'];
                $new_pass = $_POST['new_pass'];
                $confirm_pass = $_POST['confirm_pass'];

                if($new_pass != $confirm_pass){
                    echo '<p class="msg">New Passwords do not match</p>';
                }else{
                    // Create a connection object
                    $conn = new mysqli("localhost", "root", "", "nss_db");
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt1 = $conn->prepare("SELECT user_id, password FROM students WHERE user_id = ?");
                    $stmt1->bind_param("s", $reg);
                    $stmt1->execute();
                    $result = $stmt1->get_result();

                    if($result->num_rows > 0) {
                        $cred = $result->fetch_assoc();
                        if($cred['password'] != $old_pass){
                            echo '<p class="msg">Incorrect Password</p>';
                        }else{
                            $stmt2 = $conn->prepare("UPDATE students SET password = ? WHERE user_id = ?");
                            $stmt2->bind_param("ss",$new_pass,$cred['user_id']);
                            if($stmt2->execute()){
                                echo '<p class="msg">Password updated successfully</p>';
                            }else {
                                echo 'Error updating password: ' . $conn->connect_error;
                            }
                        }
                    }else{
                        echo 'No User Found';
                        header("Location: login.php");
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
