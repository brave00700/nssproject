<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "staff_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    // Input validation
    if (empty($user_id) || empty($new_password)) {
        echo "<p style='color:red;'>User ID and Password cannot be empty.</p>";
    } else {
        // Update query
        $sql = "UPDATE staff_details SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Password updated successfully!');</script>";
            } else {
                echo "<script>alert('User ID not found or no changes made.');</script>";
            }
        } else {
            echo "<p style='color:red;'>Error updating password: " . $conn->error . "</p>";
        }

        $stmt->close();
    }
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
    <link rel="stylesheet" href="adminportal.css">
   
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
            <li><a href="manage_announcements.php">Manage Announcements</a></li>
            <li><a class="active"  href="manage_passwords.php">Accounts & Passwords</a></li>
            <li><a href="">####</a></li>
            <li><a href="">####</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="create_po_exe_account.php">Create PO & Executive Account</a></li>
            <li><a  href="view_po_exe_account.php">View PO & Executive Account</a></li>
            <li><a  href="search_student.php">Search a Student</a></li>
            <li><a href="view_admitted_students.php">View Admitted Students<br> (Unit-wise)</a></li>
            <li><a href="modify_students_details.php">Modify Students Details</a></li>
            <li><a href="change_student_password.php">Change Student Password</a></li>
            <li><a class="active" href="change_EXE_PO_password.php">Change Executive & Program Officer Password</a></li>
            
          </ul>
        </div>
        <div class="widget">
            <div id="change_password">
        <h2>Change Executive & Program Officer Password</h2>
    <form method="POST">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required><br><br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <button type="submit">Change Password</button>
    </form></div>
        </div>
    </div>
</div>
</body>
</html>
