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

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $register_no = $_POST['register_no'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $unit = isset($_POST['unit']) && $_POST['unit'] !== '' ? intval($_POST['unit']) : null;

    // Handle file upload
    $profilePhoto = '';
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }
        $profilePhoto = $uploadDir . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profilePhoto);
    }

    // Build the SQL query dynamically
    $updates = [];
    if (!empty($name)) $updates[] = "Name = '$name'";
    if (!empty($register_no)) $updates[] = "Register_no = '$register_no'";
    if (!empty($phone)) $updates[] = "Phone = '$phone'";
    if (!empty($email)) $updates[] = "Email = '$email'";
    if (!empty($dob)) $updates[] = "DoB = '$dob'";
    if (!empty($gender)) $updates[] = "Gender = '$gender'";
    if (!empty($address)) $updates[] = "Address = '$address'";
    if (!empty($role)) $updates[] = "role = '$role'";
    if (!is_null($unit)) $updates[] = "Unit = $unit";
    if (!empty($profilePhoto)) $updates[] = "ProfilePhoto = '$profilePhoto'";

    if (count($updates) > 0) {
        $sql = "UPDATE staff_details SET " . implode(", ", $updates) . " WHERE User_id = '$user_id'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Staff details updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('No changes were made.');</script>";
    }
}

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
    <link rel="stylesheet" href="adminportal.css">

</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1><b style="font-size: 2.9rem;">National Service Scheme </b><br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
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
            <li><a   href="manage_students.php"> Manage Students</a></li>
            <li><a class="active" href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="create_po_exe_account.php">Create PO & Executive Account</a></li>
            <li><a href="view_po_exe_account.php">View PO & Executive Account</a></li>
            <li><a class="active"  href="modify_po_exe_details.php">Modify PO & Executive Details</a></li>
           
            <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
          </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify PO & Executive Details</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="nss-form">
                <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required><br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name"><br><br>

        <label for="register_no">Register Number:</label>
        <input type="text" id="register_no" name="register_no"><br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone"><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob"><br><br>

        <label for="gender">Gender:</label>
        <input type="text" id="gender" name="gender"><br><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address"></textarea><br><br>

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

        <label for="profile_photo">Profile Photo:</label>
        <input type="file" id="profile_photo" name="profile_photo"><br><br>

        
            
                    <div class="form-buttons">
                    <button type="submit">Update Staff Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
