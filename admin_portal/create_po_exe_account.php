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
        $maxFileSize = 512  * 1024; // 500kb

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 500kb limit. " . $fileSize . " ');</script>";
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
        $profilePhoto = '/assets/uploads/profile_photo/' . $fileName;
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
            <li><a href="manage_reports.php">Reports & Register</a></li>
            <li><a  href="manage_more.php"> More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
            <li><a class="active" href="manage_staff.php">PO & Executive Account</a></li>
            
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
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    // Prevent form submission if validation fails
    if (!validateForm()) {
        e.preventDefault();
    }
});

function validateForm() {
    // Regular expressions for validation
    const nameRegex = /^[A-Za-z\s]{2,50}$/;
    const phoneRegex = /^[6-9]\d{9}$/;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const userIdRegex = /^[a-zA-Z0-9_]{4,20}$/;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;

    // Get form elements
    const name = document.getElementById('name');
    const phone = document.getElementById('phone');
    const email = document.getElementById('email');
    const dob = document.getElementById('dob');
    const gender = document.getElementById('gender');
    const role = document.getElementById('role');
    const unit = document.getElementById('unit');
    const userId = document.getElementById('user_id');
    const password = document.getElementById('password');
    const profilePhoto = document.getElementById('profile_photo');
    
    // Validate Name
    if (!nameRegex.test(name.value)) {
        alert('Name must be 2-50 letters only (no numbers or special characters)');
        name.focus();
        return false;
    }

    // Validate Phone
    if (!phoneRegex.test(phone.value)) {
        alert('Please enter a valid 10-digit Indian phone number starting with 6-9');
        phone.focus();
        return false;
    }

    // Validate Email
    if (!emailRegex.test(email.value)) {
        alert('Please enter a valid email address');
        email.focus();
        return false;
    }

    // Validate Date of Birth
    const dobDate = new Date(dob.value);
    const today = new Date();
    const minAgeDate = new Date();
    minAgeDate.setFullYear(today.getFullYear() - 18); // Minimum 18 years old
    
    if (dobDate >= today || dobDate >= minAgeDate) {
        alert('Staff must be at least 18 years old');
        dob.focus();
        return false;
    }

    // Validate Gender
    if (gender.value === '') {
        alert('Please select gender');
        gender.focus();
        return false;
    }

    // Validate Role
    if (role.value === '') {
        alert('Please select role');
        role.focus();
        return false;
    }

    // Validate Unit
    if (unit.value === '') {
        alert('Please select unit');
        unit.focus();
        return false;
    }

    // Validate User ID
    if (!userIdRegex.test(userId.value)) {
        alert('User ID must be 4-20 characters (letters, numbers, underscores only)');
        userId.focus();
        return false;
    }

    // Validate Password
    if (!passwordRegex.test(password.value)) {
        alert('Password must be at least 8 characters with at least one letter and one number');
        password.focus();
        return false;
    }

    // Validate Profile Photo
    if (profilePhoto.files.length > 0) {
        const file = profilePhoto.files[0];
        const maxSize = 512 * 1024; // 512kb
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        if (file.size > maxSize) {
            alert('Profile photo must be less than 2MB');
            profilePhoto.focus();
            return false;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, JPEG, and PNG images are allowed');
            profilePhoto.focus();
            return false;
        }
    } else {
        alert('Please upload a profile photo');
        profilePhoto.focus();
        return false;
    }

    return true;
}
</script>
<script src="script.js"></script>
</body>
</html>