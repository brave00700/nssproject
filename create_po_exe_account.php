<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: login.html");
}            ?>
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "staff_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $register_no = $_POST['register_no'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $user_id = $_POST['user_id'];
    $password = $_POST['Password'];
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

        // Define the upload directory
        $uploadDir = 'uploads/profile_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . basename($fileName);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profilePhoto = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO staff_details 
            (Name, Register_no, Phone, Email, DoB, Gender, Address,role ,User_id,Password, ProfilePhoto,Unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssi",
        $name,
        $register_no,
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
        echo "<script>alert('Application submitted successfully!');</script>";
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
    <link rel="stylesheet" href="style.css">
   
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Admin Portal</b><br>
        </h1> 
        <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
        <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="view_admitted_students.php"> Manage Students</a></li>
            <li><a class="active" href="view_po.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a  href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
            <li><a class="active" href="view_po.php">View PO & Executive Account</a></li>
            
            
            <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
            
          </ul>
        </div>

        <div class="widget">
    <div class="mainapply">
      <h2>Create PO & Executive Account</h2>
      <form action="" method="post" class="nss-form" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="register_no">Register Number: (if executive account)</label>
        <input type="text" id="register_no" name="register_no"   />

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
          <option value="Executive">Executive</option>
          <option value="Program_Officer">Program Officer</option>
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
        <label for="Password">Password:</label>
        <input type="text" id="Password" name="Password" required/>

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
</body>
</html>
+
