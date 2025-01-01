<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_application";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register_no = $_POST['register_no'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $age = isset($_POST['age']) && $_POST['age'] !== '' ? intval($_POST['age']) : null;
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $category = $_POST['category'];
    $bloodgroup = $_POST['bloodgroup'];
    $shift = isset($_POST['shift']) && $_POST['shift'] !== '' ? intval($_POST['shift']) : null;
    $course = $_POST['course'];
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
    if (!empty($father_name)) $updates[] = "Father_name = '$father_name'";
    if (!empty($mother_name)) $updates[] = "Mother_name = '$mother_name'";
    if (!empty($phone)) $updates[] = "Phone = '$phone'";
    if (!empty($email)) $updates[] = "Email = '$email'";
    if (!is_null($age)) $updates[] = "Age = $age";
    if (!empty($gender)) $updates[] = "Gender = '$gender'";
    if (!empty($address)) $updates[] = "Address = '$address'";
    if (!empty($category)) $updates[] = "Category = '$category'";
    if (!empty($bloodgroup)) $updates[] = "Bloodgroup = '$bloodgroup'";
    if (!is_null($shift)) $updates[] = "Shift = $shift";
    if (!empty($course)) $updates[] = "Course = '$course'";
    if (!is_null($unit)) $updates[] = "Unit = $unit";
    if (!empty($profilePhoto)) $updates[] = "ProfilePhoto = '$profilePhoto'";

    if (count($updates) > 0) {
        $sql = "UPDATE admitted_students SET " . implode(", ", $updates) . " WHERE Register_no = '$register_no'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Student details updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";

        }
    } else {
        echo "<script>alert('No changes were made.);</script>";
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
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="manage_inventory.php">Inventory</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
            <li><a  href="search_student.php">Search a Student</a></li>
            <li><a href="view_admitted_students.php">View Admitted Students<br> (Unit-wise)</a></li>           
            <li><a class="active" href="modify_students_details.php">Modify Students Details</a></li>
            <li><a href="change_student_password.php">Change Student Password</a></li>
           
          </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify Admitted Student Details</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="nss-form">
                    <label for="register_no">Register Number:</label>
                    <input type="text" id="register_no" name="register_no" required><br><br>
            
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name"><br><br>
            
                    <label for="father_name">Father's Name:</label>
                    <input type="text" id="father_name" name="father_name"><br><br>
            
                    <label for="mother_name">Mother's Name:</label>
                    <input type="text" id="mother_name" name="mother_name"><br><br>
            
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone"><br><br>
            
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"><br><br>
            
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age"><br><br>
            
                    <label for="gender">Gender:</label>
                    <input type="text" id="gender" name="gender"><br><br>
            
                    <label for="address">Address:</label>
                    <textarea id="address" name="address"></textarea><br><br>
            
                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category"><br><br>
            
                    <label for="bloodgroup">Blood Group:</label>
                    <input type="text" id="bloodgroup" name="bloodgroup"><br><br>
            
                    <label for="shift">Shift:</label>
                    <input type="number" id="shift" name="shift"><br><br>
            
                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course"><br><br>
            
                    <label for="unit">Unit:</label>
                    <input type="number" id="unit" name="unit"><br><br>
            
                    <label for="profile_photo">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo"><br><br>
            
                    <div class="form-buttons">
                        <button type="submit">Update Student Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
