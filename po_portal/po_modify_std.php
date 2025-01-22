<?php
session_start();

// Redirect if not logged in
if (!$_SESSION['po_id']) {
    header("Location: ../login.html");
}

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

// Initialize variables
$student = [];
$register_no = null;

// Handle modify request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify']) && isset($_POST['register_no'])) {
    // Since `register_no` is an array, process only the first selected student
    $register_no_array = $_POST['register_no'];

    if (count($register_no_array) > 1) {
        echo "<script>alert('Please select only one student to modify.'); window.location.href = 'view_admitted_students.php';</script>";
        exit;
    }

    $register_no = $register_no_array[0]; // Get the selected register number
}

// Fetch details for the selected student
if ($register_no) {
    $sql = "SELECT * FROM admitted_students WHERE Register_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $register_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<script>alert('No student found with the selected register number.'); window.location.href = 'view_admitted_students.php';</script>";
        exit;
    }
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_details'])) {
    // Fetch and validate input fields
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
            mkdir($uploadDir, 0777, true);
        }
        $profilePhoto = $uploadDir . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profilePhoto);
    }

    // Build the SQL query dynamically
    $updates = [];
    if (!empty($name)) $updates[] = "Name = ?";
    if (!empty($father_name)) $updates[] = "Father_name = ?";
    if (!empty($mother_name)) $updates[] = "Mother_name = ?";
    if (!empty($phone)) $updates[] = "Phone = ?";
    if (!empty($email)) $updates[] = "Email = ?";
    if (!is_null($age)) $updates[] = "Age = ?";
    if (!empty($gender)) $updates[] = "Gender = ?";
    if (!empty($address)) $updates[] = "Address = ?";
    if (!empty($category)) $updates[] = "Category = ?";
    if (!empty($bloodgroup)) $updates[] = "Bloodgroup = ?";
    if (!is_null($shift)) $updates[] = "Shift = ?";
    if (!empty($course)) $updates[] = "Course = ?";
    if (!is_null($unit)) $updates[] = "Unit = ?";
    if (!empty($profilePhoto)) $updates[] = "ProfilePhoto = ?";

    if (count($updates) > 0) {
        $sql = "UPDATE admitted_students SET " . implode(", ", $updates) . " WHERE Register_no = ?";
        $stmt = $conn->prepare($sql);

        $params = [];
        if (!empty($name)) $params[] = $name;
        if (!empty($father_name)) $params[] = $father_name;
        if (!empty($mother_name)) $params[] = $mother_name;
        if (!empty($phone)) $params[] = $phone;
        if (!empty($email)) $params[] = $email;
        if (!is_null($age)) $params[] = $age;
        if (!empty($gender)) $params[] = $gender;
        if (!empty($address)) $params[] = $address;
        if (!empty($category)) $params[] = $category;
        if (!empty($bloodgroup)) $params[] = $bloodgroup;
        if (!is_null($shift)) $params[] = $shift;
        if (!empty($course)) $params[] = $course;
        if (!is_null($unit)) $params[] = $unit;
        if (!empty($profilePhoto)) $params[] = $profilePhoto;
        $params[] = $register_no;

        $stmt->bind_param(str_repeat("s", count($params)), ...$params);

        if ($stmt->execute()) {
            echo "<script>alert('Student details updated successfully!'); window.location.href = 'po_view_admitted_students.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../adminportal.css">
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b><br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
        <b style="font-size: 1.3rem">Program Officer Portal</b><br>
    </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
<ul>
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a class="active" href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a href=".php"> ####</a></li>
            <li><a href="po_logout.php">Logout</a></li>
        </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  class="active" href="view_admitted_students.php">View Admitted Students</a></li>
                
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify Admitted Student Details</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="nss-form">
                    <label for="register_no">Register Number:</label>
                    <input type="text" id="register_no" name="register_no" value="<?= $student['Register_no'] ?? '' ?>" required readonly>
                    

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?= $student['Name'] ?? '' ?>"><br><br>

                    <label for="father_name">Father's Name:</label>
                    <input type="text" id="father_name" name="father_name" value="<?= $student['Father_name'] ?? '' ?>"><br><br>

                    <label for="mother_name">Mother's Name:</label>
                    <input type="text" id="mother_name" name="mother_name" value="<?= $student['Mother_name'] ?? '' ?>"><br><br>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?= $student['Phone'] ?? '' ?>"><br><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $student['Email'] ?? '' ?>"><br><br>

                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" value="<?= $student['Age'] ?? '' ?>"><br><br>

                    <label for="gender">Gender:</label>
                    <input type="text" id="gender" name="gender" value="<?= $student['Gender'] ?? '' ?>"><br><br>

                    <label for="address">Address:</label>
                    <textarea id="address" name="address"><?= $student['Address'] ?? '' ?></textarea><br><br>

                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category" value="<?= $student['Category'] ?? '' ?>"><br><br>

                    <label for="bloodgroup">Select Blood group:</label>
                    <select id="bloodgroup" name="bloodgroup">
                        <option value="" disabled>Select</option>
                        <option value="A+" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'AB-' ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= isset($student['Bloodgroup']) && $student['Bloodgroup'] === 'O-' ? 'selected' : '' ?>>O-</option>
                    </select><br><br>

                    <label for="shift">Select Shift:</label>
                    <select id="shift" name="shift">
                        <option value="" disabled>Select</option>
                        <option value="1" <?= isset($student['Shift']) && $student['Shift'] === '1' ? 'selected' : '' ?>>Shift 1</option>
                        <option value="2" <?= isset($student['Shift']) && $student['Shift'] === '2' ? 'selected' : '' ?>>Shift 2</option>
                        <option value="3" <?= isset($student['Shift']) && $student['Shift'] === '3' ? 'selected' : '' ?>>Shift 3</option>
                    </select><br><br>

                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" value="<?= $student['Course'] ?? '' ?>"><br><br>

                    <label for="unit">Select Unit:</label>
                    <select id="unit" name="unit" disabled>
                        <option value="" disabled>Select Unit</option>
                        <option value="1" <?= isset($student['Unit']) && $student['Unit'] == '1' ? 'selected' : '' ?>>Unit 1</option>
                        <option value="2" <?= isset($student['Unit']) && $student['Unit'] == '2' ? 'selected' : '' ?>>Unit 2</option>
                        <option value="3" <?= isset($student['Unit']) && $student['Unit'] == '3' ? 'selected' : '' ?>>Unit 3</option>
                        <option value="4" <?= isset($student['Unit']) && $student['Unit'] == '4' ? 'selected' : '' ?>>Unit 4</option>
                        <option value="5" <?= isset($student['Unit']) && $student['Unit'] == '5' ? 'selected' : '' ?>>Unit 5</option>
                    </select><br><br>

                    <label for="profile_photo">Profile Photo:</label>
                    <input type="file" id="profile_photo" name="profile_photo"><br><br>

                    <div class="form-buttons">
                        <button type="submit" name="update_details" >Update Student Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
