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
            <?php
            // Creating a new session
            session_start();

            // Storing session variable
            if(!$_SESSION['reg']){
                header("Location: std_login.php");
            }
            $reg = $_SESSION['reg'];

            if(!$_SESSION['event_name']){
                header("Location: std_attendance_apply.php");
            }
            $event_name = $_SESSION['event_name'];

            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "event_db");
            $conn1 = new mysqli("localhost", "root", "", "nss_application");

            $student_query = $conn1->query("SELECT Name,Unit FROM admitted_students WHERE Register_no = '$reg'");
            $event_query = $conn->query("SELECT teacher_incharge, event_type, event_duration, event_date FROM events WHERE event_name = '$event_name'");

            $student_details = $student_query->fetch_assoc();
            $event_details = $event_query->fetch_assoc();

            echo "<form method='POST' enctype='multipart/form-data' name='att_form'>
                    <label for='student_name'>Name</label>
                    <input type='text' id='student_name' name='student_name' value='{$student_details['Name']}' readonly><br>

                    <label for='reg'>Register No</label>
                    <input type='text' id='reg' name='reg' value='{$reg}' readonly><br>

                    <label for='unit'>Unit</label>
                    <input type='text' id='unit' name='unit' value='{$student_details['Unit']}' readonly><br>

                    <label for='event_name'>Event Name</label>
                    <input type='text' id='event_name' name='event_name' value='{$event_name}' readonly><br>

                    <label for='event_date'>Event Date</label>
                    <input type='date' id='event_date' name='event_date' value='{$event_details['event_date']}' readonly><br>

                    <label for='event_duration'>Event Duration</label>
                    <input type='number' id='event_duration' name='event_duration' value='{$event_details['event_duration']}' size='2' readonly><br>

                    <label for='teacher_inc'>Teacher Incharge</label>
                    <input type='text' id='teacher_inc' name='teacher_inc' value='{$event_details['teacher_incharge']}' readonly><br>

                    <label for='event_type'>Event Type</label>
                    <input type='text' id='event_type' name='event_type' value='{$event_details['event_type']}' readonly><br>

                    <label for='photo'>Upload Proof</label>
                    <input type='file' id='photo' name='photo' accept='image/jpeg, image/png, image/jpg' required><br>

                    <button type='submit' name='att_submit'>Submit</button>
                </form>";

                $conn->close();
                $conn1->close();
            
            ?>
        </div>  
    </div>
</div>
<script></script>


<?php
$conn2 = new mysqli("localhost", "root", "", "attendance_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

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
    $stmt1 = $conn2->prepare("SELECT * FROM attendance WHERE event_name = ? AND reg_no = ?");
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
        $uploadDir = 'attendance/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . basename($fileName);

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
            (photo_path, name, reg_no, unit, event_name, event_date, duration_hrs, teacher_incharge, event_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn2->prepare($sql);
    $stmt->bind_param(
        "sssississ",
        $a_photo,
        $a_name,
        $a_reg,
        $a_unit,
        $a_event_name,
        $a_event_date,
        $a_event_duration,
        $a_teacher_inc,
        $a_event_type
    );

    // Execute query
    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!');</script>";
        
        // Delete event_name
        unset($_SESSION['event_name']);
        
        header("Location: std_attendance_apply.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
    
}
?>

</body>
</html>