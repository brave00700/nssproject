<?php
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $unit=$_POST['unit'];
    $profilePhoto = null;

    // Handle file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
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

         // Handle file upload
$profilePhoto = '';
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../assets/uploads/profile_photo/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = basename($_FILES['profile_photo']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $filePath)) {
        // Store path in database without '../'
        $profilePhoto = 'assets/uploads/profile_photo/' . $fileName;
    }
    

      
    }
  }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO staff
            (name, phone, email, dob, gender, address,role ,user_id,password, profile_photo,unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssi",
        $name,
       
        $phone,
        $email,
        $dob,
        $gender,
        $address,
        $role,
        $user_id,
        $password,
        $profilePhoto,
        $unit
    );

    // Execute query
    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!'); window.location.href = 'manage_staff.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
   
</head>
<body>
<header>
  <div class="header-container">
    <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
    <div class="header-content">
      <div class="header-text">NATIONAL SERVICE SCHEME</div>
      <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
      <div class="header-subtext">ADMIN PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
        <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a class="active" href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a  href="manage_more.php"> More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
            <li><a class="active" href="view_po.php">PO & Executive Account</a></li>
            
            <li><a href="po_leave.php">PO leave</a></li> 
            <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
            
          </ul>
        </div>

        <div class="widget">
    <div class="mainapply">
      <h2>Create PO & Executive Account</h2>
      <form action="" method="post" class="nss-form" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required />

        

        <label for="phone">Phone Number:</label>
        <input type="number" id="phone" name="phone" required />

        <label for="email">Email ID:</label>
        <input type="email" id="email" name="email" required />

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob"  required/>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required >
          <option value="" disabled selected>Select </option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required/>

        <label for="role">Role:</label>
        <select id="role" name="role" required >
          <option value="" disabled selected>Select </option>
          <option value="EXECUTIVE">Executive</option>
          <option value="PO">Program Officer</option>
        </select>

        <label for="unit">Unit:</label>
        <select id="unit" name="unit" required >
          <option value="" disabled selected>Select </option>
          <option value="1">1</option>
          <option value="2">2 </option>
          <option value="3">3 </option>
          <option value="4">4 </option>
          <option value="5">5 </option>
        </select>

        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required/>
        <label for="password">Password:</label>
        <input type="text" id="password" name="password" required/>

        <label for="profile_photo">Profile Photo (JPEG, PNG, JPG, max size: 2MB):</label>
    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/jpg" required  />


        <div class="form-buttons">
          <button type="submit">Submit</button>
          <button type="reset">Reset</button>
        </div>
      </form>
      </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>