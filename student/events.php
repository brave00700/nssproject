<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    .tile {
        border: 1px solid #303a8394;
        padding: 10px;
        display: flex;
        margin-bottom: 10px;
        background-color: #303983;
        color: #FFFFFF;
        border-radius: 5px;
    }
    .tile a {
        flex: 0.2;
        display: inline-block;
        text-decoration: none;
    }
    .tile img {
        display: block;
        height: 100%;
        width: 100%;
        object-fit: fill;
    }
    .tile-content{
        flex:0.8;
        padding: 0 5px;
        display: flex;
        flex-direction: column;
    }
    span{
        line-height: 1.1rem;
        overflow-y: hidden;
        font-size: 1rem;
        flex: 1;
    }
    span.e_name {
        font-size: 1.6rem;
        flex: none;
        line-height: 1.6rem;
    }
    span.e_name span {
        text-transform:uppercase;
        font-size: inherit;
    }
</style>
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
            <li><a href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a class="active" href="events.php">Events</a></li>
            <li><a  href="griev.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="events.php">View Events</a></li>
          </ul>
        </div>
        <div class="widget">
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

            // Create a connection object for retrieving student unit no
            $conn = new mysqli("localhost", "root", "", "nss_db");
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt1 = $conn->prepare("SELECT unit FROM students WHERE register_no = ?");
            $stmt1->bind_param("s",$reg);
            $stmt1->execute();
            $result_student = $stmt1->get_result();
            if($result_student->num_rows > 0){
                $row = $result_student->fetch_assoc();
                $unit = $row['unit'];
            }

            $stmt2 = $conn->prepare("SELECT event_name, event_date, event_time, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue FROM events WHERE event_unit = ? OR event_unit = 'All'");
            $stmt2->bind_param("s",$unit);
            $stmt2->execute();
            $result_event = $stmt2->get_result();
            
            if($result_event->num_rows > 0){
                while($row = $result_event->fetch_assoc()){
                    echo "<div class='tile'>
                <a href='../{$row['poster_path']}' target='_blank'><img src='../{$row['poster_path']}'></a>
                <div class='tile-content'>
                    <span class='e_name'>Event Name: <span>{$row['event_name']}</span></span>
                    <span class='e_desc'>Description: {$row['event_desc']}</span>
                    <span class='e_date_time'>Event Date/Time: {$row['event_date']} {$row['event_time']}</span>
                    <span class='e_venue'>Event Venue: {$row['event_venue']}</span>
                    <span class='e_type'>Event Type: {$row['event_type']}</span>
                    <span class='e_tinc'>Teacher Incharge: {$row['teacher_incharge']}</span>
                    <span class='e_sinc'>Student Incharge: {$row['student_incharge']}</span>
                </div>
            </div>";
                }
            }else{
                echo "<script>document.querySelector('.widget').innerHTML += 'No Events Found';</script>";
            }

            $stmt1->close();
            $stmt2->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
