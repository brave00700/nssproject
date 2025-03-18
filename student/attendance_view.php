<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();

$conn = getDatabaseConnection();

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
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow-x: auto; /* Enables horizontal scrolling */
}

/* Add a scrollable wrapper */
.widget .table-container {
    overflow-x: auto;
    width: 100%;
}

/* Table Styling */
.widget table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-size: 1rem;
    min-width: 600px; /* Prevents table from shrinking too much */
}

.widget table thead {
    background: #ffbf2e;
    color: #ffffff;
    font-weight: bold;
}

.widget table tr:first-child td {
    background: #ffbf2e;
    color: #ffffff;
    font-weight: bold;
    padding: 12px;
}

.widget table tr:nth-child(even) {
    background: #fff1d3;
}

.widget table tr:nth-child(odd) {
    background: #ffe6b3;
}

.widget table td {
    padding: 10px;
    border: 1px solid #fff;
}

/* Link Styling */
.widget table a {
    text-decoration: none;
    font-weight: bold;
    color: #007bff;
    transition: 0.3s;
}

.widget table a:hover {
    color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .widget {
        padding: 1rem;
    }

    .widget .table-container {
        overflow-x: auto;
    }

    .widget table {
        font-size: 0.9rem;
        min-width: 600px; /* Ensures the table scrolls instead of shrinking */
    }

    .widget table td {
        padding: 8px;
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
            <li><a class="active" href="attendance_view.php">View Attendance</a></li>
            <li><a href="attendance_apply.php">Apply Attendance</a></li>
          </ul>
        </div>
        <div class="widget">
            <div class="table-container">
            <table class="att_list">
                <tr>
                    <td>Sl No</td>
                    <td>Event Name</td>
                    <td>Event Date</td>
                    <td>Proof</td>
                    <td>Status</td>
                </tr>
            <?php
            $stmt = $conn->prepare("SELECT events.event_name, events.event_date, attendance.photo_path, attendance.status 
            FROM attendance 
            JOIN events ON attendance.event_id = events.event_id
            WHERE attendance.register_no = ?");
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
                    <td><a href='..{$row['photo_path']}' target='_blank'>Click Here</a></td>
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
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
