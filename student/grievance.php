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
$unit = $_SESSION['unit'];

// Create a connection object 
$conn = new mysqli("localhost", "root", "", "nss_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

$inc = 1;

// Fetch existing grievances for this student
$stmt = $conn->prepare("SELECT subject, activity_type, created_at, status, comment FROM grievance WHERE student_id = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $reg);
$stmt->execute();
$result = $stmt->get_result();
$grievances = [];
while($row = $result->fetch_assoc()) {
    $grievances[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    
.grievanceList{
    width:100%;
    overflow-x: auto;
}
.widget{
    width: 100%;
    min-height: 50vh;
}
.grievance-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        table-layout: fixed;
    }
    .grievance-table th, .grievance-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    .grievance-table th {
        background-color: #f2f2f2;
    }
    .grievance-table tr:hover {
        background-color: #f5f5f5;
    }
    .status-pending {
        color: #dc3545;
        font-weight: bold;
    }
    .status-resolved {
        color: #28a745;
        font-weight: bold;
    }
    @media screen and (max-width: 768px) {
    .grievanceList {
        width: 100%;
        overflow-x: auto;
        display: block;
    }
    
    .grievance-table {
        min-width: 600px; /* Prevents extreme shrinking */
        table-layout: auto; /* Allows columns to adjust */
    }
    
    .grievance-table th, 
    .grievance-table td {
        white-space: nowrap; /* Prevents wrapping in small screens */
        padding: 8px; /* Reduce padding slightly */
    }
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
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a class="active" href="grievance.php">Grievience</a></li>
            <li><a href="credits.php">Credits</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            <li><a class="active" href="grievance.php">My Grievances</a></li>
            <li><a href="grievance_new.php">New Grievance</a></li>
            </ul>
        </div>
        <div class="widget">
        <div class="grievanceList">
            <h3>My Grievances</h3>
            <?php if(empty($grievances)): ?>
                <p>No grievances found. Use the form below to submit a new grievance.</p>
            <?php else: ?>
                <table class="grievance-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Sl No</th>
                            <th style="width: 30%;">Subject</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 10%;">Date Submitted</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 30%;">Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($grievances as $grievance): ?>
                            <tr>
                                <td><?php echo $inc++ ?></td>
                                <td><?php echo htmlspecialchars($grievance['subject']) ?></td>
                                <td><?php echo $grievance['activity_type'] ?></td>
                                <td><?php echo date('d-m-Y', strtotime($grievance['created_at'])) ?></td>
                                <td class="status-<?php echo strtolower($grievance['status']) ?>">
                                    <?php echo $grievance['status'] ?>
                                </td>
                                <td><?php echo $grievance['comment'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<script src="../assets/js/script.js"></script>
<!-- Include the Quill library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<!-- Initialize Quill editor -->
<script>
  const quill = new Quill('#editor', {
    theme: 'snow'
  });

  document.querySelector(".form").addEventListener("submit", (e) => {
    if (quill.getText().trim().length === 0) {
        e.preventDefault();
        alert("Empty body");
    }else{
    var content = quill.root.innerHTML;
    var sub = document.querySelector("textarea");
    sub.value = content;
    }
  });
</script>
</body>
</html>

<?php

function generateFileName() {
    $prefix = 'G_' . date('Ymd') . '_';

    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $randomChars = substr(str_shuffle($characters), 0, 4);

    return ($prefix . $randomChars);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $type = $_POST['grievance_type'];
    $send_to = $_POST['send_to'];
    $proof = null;

    // Handle file upload
    if (isset($_FILES['uploadpf']) && $_FILES['uploadpf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['uploadpf']['tmp_name'];
        $fileName = $_FILES['uploadpf']['name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileSize = $_FILES['uploadpf']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 2MB limit.');</script>";
            exit;
        }

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "<script>alert('Error: Invalid file type. Only JPEG, PNG, JPG and PDF are allowed.');</script>";
            exit;
        }

        // Define the upload directory
        $uploadDir = "../assets/uploads/grievance/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . generateFileName() . "." . $fileExt;


        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $proof = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }else{
        echo "<script>alert('Erroeelsfkjkl');</script>";
    }

    // Create a connection object 
    $conn = new mysqli("localhost", "root", "", "nss_db");
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("INSERT INTO grievance (student_id, unit, activity_type, subject, body, send_to, photo_pdf_path) VALUES(?,?,?,?,?,?,?);");
    $stmt->bind_param("sssssss", $reg, $unit, $type, $subject, $body, $send_to, $proof);
    if($stmt->execute()){
        echo "<script>alert('Grievance sent successfully');</script>";
    }else{
        echo "<script>alert('Failed to send');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>