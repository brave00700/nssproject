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
    /* max-width: 800px; */
    margin: 1.5rem auto;
}

.widget form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.widget .form-row {
    display: flex;
    gap: 1rem;
}

.widget .form-row > div {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.widget label {
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #333;
}

.widget input {
    padding: 0.4rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    width: 100%;
}

.widget input[readonly] {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.widget button {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 0.6rem;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.widget button:hover {
    background: #0056b3;
}

/* Responsive Design */
@media (max-width: 600px) {
    .widget .form-row {
        flex-direction: column;
        gap: 0.5rem;
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

            if(!$_SESSION['event_name']){
                header("Location: attendance_apply.php");
            }
            $event_name = $_SESSION['event_name'];

            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "nss_db");

            $student_query = $conn->query("SELECT name,unit FROM students WHERE register_no = '$reg'");
            $event_query = $conn->query("SELECT event_id, teacher_incharge, event_type, event_duration, event_date FROM events WHERE event_name = '$event_name'");

            $student_details = $student_query->fetch_assoc();
            $event_details = $event_query->fetch_assoc();

            echo "<form method='POST' enctype='multipart/form-data' name='att_form'>
    <div class='form-row'>
        <div>
            <label for='student_name'>Name</label>
            <input type='text' id='student_name' name='student_name' value='{$student_details['name']}' readonly>
        </div>
        <div>
            <label for='reg'>Register No</label>
            <input type='text' id='reg' name='reg' value='{$reg}' readonly>
        </div>
    </div>

    <div class='form-row'>
        <div>
            <label for='unit'>Unit</label>
            <input type='text' id='unit' name='unit' value='{$student_details['unit']}' readonly>
        </div>
        <div>
            <label for='event_name'>Event Name</label>
            <input type='text' id='event_name' name='event_name' value='{$event_name}' readonly>
        </div>
    </div>

    <div class='form-row'>
        <div>
            <label for='event_date'>Event Date</label>
            <input type='date' id='event_date' name='event_date' value='{$event_details['event_date']}' readonly>
        </div>
        <div>
            <label for='event_duration'>Event Duration</label>
            <input type='number' id='event_duration' name='event_duration' value='{$event_details['event_duration']}' readonly>
        </div>
    </div>

    <div class='form-row'>
        <div>
            <label for='teacher_inc'>Teacher Incharge</label>
            <input type='text' id='teacher_inc' name='teacher_inc' value='{$event_details['teacher_incharge']}' readonly>
        </div>
        <div>
            <label for='event_type'>Event Type</label>
            <input type='text' id='event_type' name='event_type' value='{$event_details['event_type']}' readonly>
        </div>
    </div>

    <label for='photo'>Upload Proof</label>
    <input type='file' id='photo' name='photo' accept='image/jpeg, image/png, image/jpg' required>

    <button type='submit' name='att_submit'>Submit</button>
</form>";

                $event_id = $event_details['event_id'];
            
            ?>
        </div>  
    </div>
</div>
<script></script>


<?php

if(isset($_POST['att_submit'])){
    $a_name = $_POST['student_name'];
    $a_reg = $_POST['reg'];
    $a_unit = $_POST['unit'];
    $a_event_name = $_POST['event_name'];
    $a_event_date = $_POST['event_date'];
    $a_event_duration = $_POST['event_duration'];
    $a_teacher_inc = $_POST['teacher_inc'];
    $a_event_type = $_POST['event_type'];
    $a_photo = null;

    // Check if there is an entry already
    $stmt1 = $conn->prepare("SELECT * FROM attendance 
    JOIN events ON attendance.event_id = events.event_id
    WHERE events.event_name = ? AND register_no = ?");
    $stmt1->bind_param("ss", $a_event_name, $a_reg);
    $stmt1->execute();
    $res = $stmt1->get_result();
    if($res->num_rows == 1){
        echo "<script>alert('Error: Entry already exists');</script>";
        $stmt1->close();
    }else{

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileSize = $_FILES['photo']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 2MB limit.');</script>";
            exit;
        }

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "<script>alert('Error: Invalid file type. Only JPEG, PNG, JPG are allowed.');</script>";
            exit;
        }

        // Define the upload directory
        $uploadDir = "../assets/uploads/attendance/{$a_event_name}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . $a_reg . "." . $fileExt;


        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $a_photo = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }else{
        echo "<script>alert('Erroeelsfkjkl');</script>";
    }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO attendance 
            (photo_path, event_id, register_no) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sis",
        $a_photo,
        $event_id,
        $a_reg
    );

    // Execute query
    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!');</script>";
        
        // Delete event_name
        unset($_SESSION['event_name']);
        
        header("Location: attendance_apply.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
    
}
?>
<script src="../assets/js/script.js"></script>
</body>
</html>