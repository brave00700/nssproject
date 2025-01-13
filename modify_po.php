<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit();
}

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

// Initialize variables
$staff = [];
$user_id = null;

// Fetch staff details for the given User ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
}

if ($user_id) {
    $sql = "SELECT * FROM staff_details WHERE User_id = '$user_id'";
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
    $register_no = $_POST['register_no'];
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
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
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
    if (!empty($role)) $updates[] = "Role = '$role'";
    if (!is_null($unit)) $updates[] = "Unit = $unit";
    if (!empty($profilePhoto)) $updates[] = "ProfilePhoto = '$profilePhoto'";

    if (count($updates) > 0) {
        $sql = "UPDATE staff_details SET " . implode(", ", $updates) . " WHERE User_id = '$user_id'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Staff details updated successfully!'); window.location.href = 'view_po.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "'); window.location.href = 'view_po.php';</script>";
        }
    } else {
        echo "<script>alert('No changes were made.'); window.location.href = 'view_po.php';</script>";
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
            <li><a href="view_admitted_students.php">Manage Students</a></li>
            <li><a class="active" href="view_po.php">Manage Staff</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>
            <li><a href="manage_events.php">Events</a></li>
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
    <h2>Modify PO & Executive Details</h2>
    <form action="" method="POST" enctype="multipart/form-data"  class="nss-form">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" value="<?= $staff['User_id'] ?? '' ?>" required readonly><br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= $staff['Name'] ?? '' ?>"><br><br>

        <label for="register_no">Register Number:</label>
        <input type="text" id="register_no" name="register_no" value="<?= $staff['Register_no'] ?? '' ?>"><br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= $staff['Phone'] ?? '' ?>"><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $staff['Email'] ?? '' ?>"><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= $staff['DoB'] ?? '' ?>"><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="" disabled>Select</option>
            <option value="Male" <?= ($staff['Gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($staff['Gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
        </select><br><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address"><?= $staff['Address'] ?? '' ?></textarea><br><br>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="" disabled selected>Select</option>
            <option value="Executive" <?= ($staff['role'] === 'Executive') ? 'selected' : '' ?>>Executive</option>
            <option value="Program_Officer" <?= ($staff['role'] === 'Program_Officer') ? 'selected' : '' ?>>Program Officer</option>
        </select><br><br>

        <label for="unit">Unit:</label>
        <select id="unit" name="unit" required>
            <option value="" disabled selected>Select Unit</option>
            <option value="1" <?= ($staff['Unit'] === '1') ? 'selected' : '' ?>>Unit 1</option>
            <option value="2" <?= ($staff['Unit'] === '2') ? 'selected' : '' ?>>Unit 2</option>
            <option value="3" <?= ($staff['Unit'] === '3') ? 'selected' : '' ?>>Unit 3</option>
            <option value="4" <?= ($staff['Unit'] === '4') ? 'selected' : '' ?>>Unit 4</option>
            <option value="5" <?= ($staff['Unit'] === '5') ? 'selected' : '' ?>>Unit 5</option>
        </select><br><br>

        <label for="profile_photo">Profile Photo:</label>
        <input type="file" id="profile_photo" name="profile_photo"><br><br>

        <button type="submit" name="update_details">Update Details</button>
    </form>
</div>

            </div>
        </div>
    </div>
</body>
</html>
