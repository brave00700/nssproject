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
            $result_event = $conn_event->query("SELECT event_name, event_date, event_duration FROM events");

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
</body>
</html>

<?php
if(isset($_POST['event_submit'])){
    $_SESSION['event_name'] = $_POST['event_name'];
    header("Location: std_attendance_apply_confirm.php");
}
?>