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

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$staff = [];
$user_id = null;

// Fetch staff details for the given User ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
}

if ($user_id) {
    $sql = "SELECT * FROM staff WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $staff = $result->fetch_assoc();
    } else {
        echo "<script>alert('No staff found with the entered User ID.');</script>";
    }
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_details'])) {
    // Fetch and validate input fields
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $unit = isset($_POST['unit']) ? intval($_POST['unit']) : null;

   
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

    // Build the SQL query dynamically
    $updates = [];
    if (!empty($name)) $updates[] = "name = '$name'";
    
    if (!empty($phone)) $updates[] = "phone = '$phone'";
    if (!empty($email)) $updates[] = "email = '$email'";
    if (!empty($dob)) $updates[] = "dob = '$dob'";
    if (!empty($gender)) $updates[] = "gender = '$gender'";
    if (!empty($address)) $updates[] = "address = '$address'";
    if (!empty($role)) $updates[] = "role = '$role'";
    if (!is_null($unit)) $updates[] = "unit = $unit";
    if (!empty($profilePhoto)) $updates[] = "profile_photo = '$profilePhoto'";

    if (count($updates) > 0) {
        $sql = "UPDATE staff SET " . implode(", ", $updates) . " WHERE user_id = '$user_id'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Staff details updated successfully!'); window.location.href = 'manage_staff.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "'); window.location.href = 'manage_staff.php';</script>";
        }
    } else {
        echo "<script>alert('No changes were made.'); window.location.href = 'manage_staff.php';</script>";
    }
}
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify PO & Executive Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../adminportal.css">
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
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a class="active" href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>
            <li><a href="manage_more.php">More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="about_main_divide">
            <div class="about_nav">
                <ul>
                   
                    <li><a class="active" href="view_po.php">PO & Executive Account</a></li>
                    <li><a  href="po_leave.php">PO leave</a></li> 
                    <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
                </ul>
            </div>
            <div class="widget">
            <div class="mainapply">
    <h2>Modify PO & Executive Details</h2>
    <form action="" method="POST" enctype="multipart/form-data"  class="nss-form">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" value="<?= $staff['user_id'] ?? '' ?>" required readonly><br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= $staff['name'] ?? '' ?>"><br><br>

        
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= $staff['phone'] ?? '' ?>"><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $staff['email'] ?? '' ?>"><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= $staff['dob'] ?? '' ?>"><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="" disabled>Select</option>
            <option value="MALE" <?= ($staff['gender'] === 'MALE') ? 'selected' : '' ?>>Male</option>
            <option value="FEMALE" <?= ($staff['gender'] === 'FEMALE') ? 'selected' : '' ?>>Female</option>
            <option value="OTHER" <?= ($staff['gender'] === 'OTHER') ? 'selected' : '' ?>>Other</option>
        </select><br><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address"><?= $staff['address'] ?? '' ?></textarea><br><br>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="" disabled selected>Select</option>
            <option value="EXECUTIVE" <?= ($staff['role'] === 'EXECUTIVE') ? 'selected' : '' ?>>Executive</option>
            <option value="PO" <?= ($staff['role'] === 'PO') ? 'selected' : '' ?>>Program Officer</option>
        </select><br><br>

        <label for="unit">Unit:</label>
        <select id="unit" name="unit" required>
            <option value="" disabled selected>Select Unit</option>
            <option value="1" <?= ($staff['unit'] === '1') ? 'selected' : '' ?>>Unit 1</option>
            <option value="2" <?= ($staff['unit'] === '2') ? 'selected' : '' ?>>Unit 2</option>
            <option value="3" <?= ($staff['unit'] === '3') ? 'selected' : '' ?>>Unit 3</option>
            <option value="4" <?= ($staff['unit'] === '4') ? 'selected' : '' ?>>Unit 4</option>
            <option value="5" <?= ($staff['unit'] === '5') ? 'selected' : '' ?>>Unit 5</option>
        </select><br><br>

        <label for="profile_photo">Profile Photo:</label>
        <input type="file" id="profile_photo" name="profile_photo"><br><br>

        <button type="submit" name="update_details">Update Details</button>
    </form>
</div>

            </div>
        </div>
    </div>
<script src="script.js"></script>
</body>
</html>
