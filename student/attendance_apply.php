<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();

$conn = getDatabaseConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_submit'])){
    $_SESSION['event_name'] = $_POST['event_name'];
    header("Location: attendance_apply_confirm.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .widget {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    /* margin: 20px auto; */
    overflow: hidden;
}

.widget table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.widget table tr:first-child {
    background: #ffbf2e;
    color: #fff;
    font-weight: bold;
    text-align: left;
}

.widget table th,
.widget table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.widget table tbody tr:hover {
    background: #fff5cc;
    transition: background 0.3s ease-in-out;
}

.widget button {
    background: #ffca3b;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease-in-out;
}

.widget button:hover {
    background: #ffba00;
}

@media (max-width: 768px) {
    .widget {
        padding: 15px;
        overflow-x: auto;
    }

    .widget table {
        font-size: 14px;
    }

    .widget table th,
    .widget table td {
        padding: 10px;
    }

    .widget button {
        padding: 6px 10px;
        font-size: 14px;
    }
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
            <li><a href="profile.php">Profile</a></li>
            <li><a class="active" href="attendance_view.php">Attendance</a></li>
            <li><a  href="events.php">Events</a></li>
            <li><a  href="grievance.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="attendance_view.php">View Attendance</a></li>
            <li><a class="active" href="attendance_apply.php">Apply Attendance</a></li>
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
            
            $stmt3 = $conn->prepare("SELECT event_name, event_id, event_date, event_duration FROM events WHERE event_unit = 'All' OR event_unit = ?");
            $stmt3->bind_param("s", $_SESSION['unit']);
            $stmt3->execute();
            $result_event = $stmt3->get_result();

            
            $stmt = $conn->prepare("SELECT events.event_name FROM attendance 
            JOIN events ON attendance.event_id = events.event_id
            WHERE register_no = ?");
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
                $current_date = new DateTime();
                while($row = $result_event->fetch_assoc()){
                    $event_date = new DateTime($row['event_date']); 
                    $deadline_date = (clone $event_date)->modify('+7 days 23:59:59');


                    if(!in_array($row['event_name'],$applied_events, true) && ($event_date < $current_date) && ($current_date < $deadline_date)){
                    echo "<tr>
                            <td>{$row['event_name']}</td>
                            <td>{$row['event_date']}</td>
                            <td>{$row['event_duration']}</td>
                            <td><button name='event_submit' type='submit'>Apply</button></td>
                            <input type='hidden' name='event_name' value='{$row['event_id']}'>
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
<script src="../assets/js/script.js"></script>
</body>
</html>
