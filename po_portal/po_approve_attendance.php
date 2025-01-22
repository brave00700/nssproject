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
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a   href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a class="active" href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a href=".php"> ####</a></li>
            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="po_approve_attendance.php">View Attendance</a></li>
            <li><a href="">###</a></li>
            <li><a href="">###</a></li>
          </ul>
        </div>
        <div class="widget">
            <table>
            <?php
          
            $po_id = $_SESSION['po_id'];
            $po_unit = $_SESSION['unit'];
            // Create a connection object
            $conn_event = new mysqli("localhost", "root", "", "event_db");
            if($conn_event->connect_error){
                die("Connection failed: " . $conn_event->connect_error);
            }
            $stmt_event = $conn_event->prepare("SELECT event_name, event_date FROM events WHERE event_unit = ? OR event_unit = 10");
            $stmt_event->bind_param("i", $po_unit);
            $stmt_event->execute();
            $result = $stmt_event->get_result();

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                echo "<tr>
                <td>{$row['event_name']}</td>
                <td>{$row['event_date']}</td>
                <td><form method='POST'>
                    <input type='hidden' name='event_name' value='{$row['event_name']}'>
                    <input type='submit' value='Approve'>
                </form></td></tr>";
                }
            }
            $stmt_event->close();
            $conn_event->close();
            ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_name'])){
    $_SESSION['att_evt_name'] = $_POST['event_name'];
    header("Location: po_approve_confirm.php");
}
?>