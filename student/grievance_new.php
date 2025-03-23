<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();
$unit = $_SESSION['unit'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
    input[name="subject"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
    outline: none;
}
.widget{
    width: 100%;
    min-height: 50vh;
}
form button {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease;
}

form button:hover {
    background-color: #0056b3;
}
/* Basic style for the dropdown */
/* Dropdown fix */
select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f8f8f8;
    cursor: pointer;
    position: relative;
}

.ql-toolbar {
    z-index: 1000 !important;
    position: relative;
}

.ql-picker {
    z-index: 1050 !important;
    position: absolute;
}

.ql-picker-options {
    z-index: 1100 !important;
    position: absolute;
    background: white;
    border: 1px solid #ccc;
}


/* Custom dropdown arrow */
select::after {
    content: " ▼";
    font-size: 12px;
    color: #007bff;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}

/* Hover and Focus state */
select:hover, select:focus {
    border-color: #007bff;
    outline: none;
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
            <li><a href="grievance.php">My Grievances</a></li>
            <li><a class="active" href="grievance_new.php">New Grievance</a></li>
            </ul>
        </div>
        <div class="widget">
        <form class="form" method="POST" enctype="multipart/form-data">
            <div class="group-input" style="display: flex; gap: 10px; width: 100%">
            <div class="input-group" style="width: 100%">
                <label for="type">Grievance-Type:</label>
                <select id="type" name="grievance_type" required>
                    <option value="" selected hidden>Select</option>
                    <option value="REGULAR" >Regular activiy</option>
                    <option value="CAMP">Camp activity</option>
                    <option value="OTHER">Other</option>
                </select>
            </div>
            </div>
            <div class="input-group">
                <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" placeholder="Eg. Grievance regarding camp" required/>
            </div>
            <textarea style="position: absolute; opacity: 0" name="body"></textarea>
        
        <label>Body:</label>
        <div id="editor" >
        
        </div>
        <div class="input-group" style="width: 100%; border: 1px solid #CCC; margin-top: 10px; border-radius: 3px; padding: 3px">
            <label>Upload Photo/PDF (Max. Size = 512KB):</label>
            <input type="file" name="uploadpf" id="uploadpf" accept="image/jpeg, image/png, image/jpg, application/pdf"/>
        </div>
        <button type="submit" name="submit" id="send">Send</button>
        </form>
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
    $proof = null;

    // Handle file upload
    if (isset($_FILES['uploadpf']) && $_FILES['uploadpf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['uploadpf']['tmp_name'];
        $fileName = $_FILES['uploadpf']['name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileSize = $_FILES['uploadpf']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $maxFileSize = 1024 * 512; // 512KB

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 512KB limit.');</script>";
            exit;
        }

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "<script>alert('Error: Invalid file type. Only JPEG, PNG, JPG and PDF are allowed.');</script>";
            exit;
        }

        // Define the upload directory
        $uploadDir = "/assets/uploads/grievance/";
        if (!is_dir(".." . $uploadDir)) {
            mkdir(".." . $uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . generateFileName() . "." . $fileExt;


        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, ".." . $filePath)) {
            $proof = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }

    // Create a connection object 
    $conn = getDatabaseConnection();
    $created_at = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO grievance (student_id, unit, activity_type, subject, body, photo_pdf_path, created_at) VALUES(?,?,?,?,?,?,?);");
    $stmt->bind_param("sssssss", $reg, $unit, $type, $subject, $body, $proof, $created_at);
    if($stmt->execute()){
        echo "<script>alert('Grievance sent successfully');</script>";
    }else{
        echo "<script>alert('Failed to send');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>