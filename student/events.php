<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();
$unit = $_SESSION['unit'];

// Create a connection object for retrieving student unit no
$conn = getDatabaseConnection();

$past_events = "";
$upcom_events = "";

$stmtEvents = $conn->prepare("SELECT event_name, event_date, event_time, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue 
FROM events 
WHERE event_unit = ? OR event_unit = 'All'
ORDER BY event_date DESC");
$stmtEvents->bind_param("s",$unit);
$stmtEvents->execute();
$resultEvents = $stmtEvents->get_result();

if($resultEvents->num_rows > 0){
    $current_date = new DateTime();
    while($row = $resultEvents->fetch_assoc()){
        $event_date = new DateTime($row["event_date"]);
        if($current_date < $event_date){
            $upcom_events .= "<div class='tile'>
                    <span class='e_name'>{$row['event_name']}</span>
                    <span class='e_date_time'>Date: {$row['event_date']} at {$row['event_time']}</span>
                    <div class='tile-content'>
                        <img src='../{$row['poster_path']}' alt='Event Poster'>
                        <span class='e_desc'><strong>Description:</strong> {$row['event_desc']}</span>
                        <span class='e_venue'><strong>Venue:</strong> {$row['event_venue']}</span>
                        <span class='e_type'><strong>Type:</strong> {$row['event_type']}</span>
                        <span class='e_tinc'><strong>Teacher Incharge:</strong> {$row['teacher_incharge']}</span>
                        <span class='e_sinc'><strong>Student Incharge:</strong> {$row['student_incharge']}</span>
                    </div>
                </div>";
        }else{
            $past_events .= "<div class='tile'>
                    <span class='e_name'>{$row['event_name']}</span>
                    <span class='e_date_time'>Date: {$row['event_date']} at {$row['event_time']}</span>
                    <div class='tile-content'>
                        <img src='../{$row['poster_path']}' alt='Event Poster'>
                        <span class='e_desc'><strong>Description:</strong> {$row['event_desc']}</span>
                        <span class='e_venue'><strong>Venue:</strong> {$row['event_venue']}</span>
                        <span class='e_type'><strong>Type:</strong> {$row['event_type']}</span>
                        <span class='e_tinc'><strong>Teacher Incharge:</strong> {$row['teacher_incharge']}</span>
                        <span class='e_sinc'><strong>Student Incharge:</strong> {$row['student_incharge']}</span>
                    </div>
                </div>";
        }
    }
}else{
    echo "<script>document.querySelector('.widget').innerHTML += 'No Events Found';</script>";
}

$stmtEvents->close();
$conn->close();
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
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    /* max-width: 800px; */
    margin: 5px auto;
}

.tile {
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
    cursor: pointer;
    transition: box-shadow 0.2s ease-in-out;
}

.tile:hover {
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

.tile .e_name {
    font-size: 1.2rem;
    font-weight: bold;
    color: #303983;
}

.tile .e_date_time {
    font-size: 0.9rem;
    color: #555;
}

/* Initially hide extra event details */
.tile-content {
    display: none;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}

/* Show details when .active is added */
.tile.active .tile-content {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}

/* Poster Image Styling */
.tile-content img {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 5px;
    margin-bottom: 10px;
}

/* Smooth Fade-in effect */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Style the details */
.tile-content span {
    display: block;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.tile-content span strong {
    color: #333;
    font-weight: bold;
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
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a class="active" href="events.php">Events</a></li>
            <li><a  href="grievance.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
        <?php
            if($upcom_events){
                echo "<div class='widget'><p>Upcoming Events</p>" . $upcom_events . "</div>";
            }
            if($past_events){
                echo "<div class='widget'><p>Past Events</p>" . $past_events . "</div>";
            }
        ?>
    </div>
</div>
<script src="../assets/js/script.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".tile").forEach(tile => {
        tile.addEventListener("click", function () {
            this.classList.toggle("active");
        });
    });
});

</script>
</body>
</html>
