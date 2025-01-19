<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
    <style>
        table tbody > tr:first-child td{
            background-color: #ffbf2ee0;
            color: #FFFFFF;
        }
        table tbody > tr td:not(:last-child){
            border-right: 1px solid #FFFFFF;
        }
        table tbody > tr:not(:first-child) td {
            background-color: #fff1d3e0;
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
            <li><a class="active" href="std_attendance_view.php">View Attendance</a></li>
            <li><a href="std_attendance_apply.php">Apply Attendance</a></li>
          </ul>
        </div>
        <div class="widget">
            <table class="att_list">
                <tr>
                    <td>Sl No</td>
                    <td>Event Name</td>
                    <td>Event Date</td>
                    <td>Proof</td>
                    <td>Status</td>
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

            // Create a connection object for attendance
            $conn_attendance = new mysqli("localhost", "root", "", "attendance_db");
            if($conn_attendance->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn_attendance->prepare("SELECT event_name, event_date, photo_path, status FROM attendance WHERE reg_no = ?");
            $stmt->bind_param("s",$reg);
            $stmt->execute();
            $result_att = $stmt->get_result();
            $sl_no = 0;
            
            if($result_att->num_rows > 0){
                $sl_no++;
                while($row = $result_att->fetch_assoc()){
                    echo "<tr>
                    <td>{$sl_no}</td>
                    <td>{$row['event_name']}</td>
                    <td>{$row['event_date']}</td>
                    <td><a href='{$row['photo_path']}' target='_blank'>Click Here</a></td>
                    <td>{$row['status']}</td>
                </tr>";
                }
            }else{
                echo "<script>document.querySelector('.att_list').style.display = 'none';
                    document.querySelector('.widget').innerHTML += 'No Applied Attendance Found';</script>";
            }
            ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
