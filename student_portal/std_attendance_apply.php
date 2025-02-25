<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
    <style>
        input {
            outline: none;
        }
        table tbody > tr:first-child td{
            background-color: #ffbf2ee0;
            color: #FFFFFF;
        }
        table tbody> tr td:not(:last-child){
            border-right: 1px solid #FFFFFF;
        }
        table tbody> tr:not(:first-child) td:not(:last-child) {
            background-color: #fff1d3e0;
        }
        button {
            width: 100%;
            border: none;
            background-color: #ffca3b;
            border-radius: 4px;
            padding: 2px 0;
            color:rgb(37, 37, 37);
        }
        button:active {
            background-color: #ffba00;
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
            <li><a href="std_profile.php">Profile</a></li>
            <li><a class="active" href="std_attendance_view.php">Attendance</a></li>
            <li><a  href="std_events.php">Events</a></li>
            <li><a  href="std_griev.php">Grievience</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="std_attendance_view.php">View Attendance</a></li>
            <li><a class="active" href="std_attendance_apply.php">Apply Attendance</a></li>
          </ul>
        </div>
        <div class="widget">
            <form method="POST" id="evt_form">
            <table>
                <tr>
                    <td>Event Name</td>
                    <td>Event Date</td>
                    <td>Event Duration</td>
                    <td>Apply</td>
                </tr>
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

            // Create a connection object for events
            $conn_event = new mysqli("localhost", "root", "", "event_db");
            if($conn_event->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt3 = $conn_event->prepare("SELECT event_name, event_date, event_duration FROM events WHERE event_unit = 10 OR event_unit = ?");
            $stmt3->bind_param("s", $_SESSION['unit']);
            $stmt3->execute();
            $result_event = $stmt3->get_result();

            // Create a connection object for attendance
            $conn_attendance = new mysqli("localhost", "root", "", "attendance_db");
            if($conn_attendance->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn_attendance->prepare("SELECT event_name FROM attendance WHERE reg_no = ?");
            $stmt->bind_param("s",$reg);
            $stmt->execute();
            $result_att = $stmt->get_result();

            // Create an array to store the events which is already applied
            $applied_events = [];
            $inc = 0;

            if($result_att->num_rows > 0){
                while($row = $result_att->fetch_assoc()){
                    $applied_events[] = $row['event_name'];
                }
            }

            if($result_event->num_rows > 0){
                while($row = $result_event->fetch_assoc()){
                    if(!in_array($row['event_name'],$applied_events, true)){
                    echo "<tr>
                            <td>{$row['event_name']}</td>
                            <td>{$row['event_date']}</td>
                            <td>{$row['event_duration']}</td>
                            <td><button name='event_submit' type='submit'>Apply</button></td>
                            <td><input type='hidden' name='event_name' value='{$row['event_name']}'></td>
                        </tr>";
                        $inc++;
                    }
                }
                if($inc == 0){
                    echo "<script>document.querySelector('#evt_form').style.display = 'none';
                    document.querySelector('.widget').innerHTML += 'No Events Found';</script>";
                }
            }else{
                echo "<script>document.querySelector('.widget').innerHTML += 'No Events Found';</script>";
            }
            
            ?>
            </table>
        </form>
        </div>  
    </div>
</div>
<script></script>
<script src="script.js"></script>
</body>
</html>

<?php
if(isset($_POST['event_submit'])){
    $_SESSION['event_name'] = $_POST['event_name'];
    header("Location: std_attendance_apply_confirm.php");
}
?>