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
if (!$_SESSION['admin_id']) {
    header("Location: ../login.html");
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

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
    $sql = "SELECT * FROM students WHERE register_no = ?";
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
    $dob = $_POST['dob']; // Added DOB field
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
        $uploadDir = '../uploads/profile_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $profilePhoto = $uploadDir . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profilePhoto);
    }

    // Build the SQL query dynamically
    $updates = [];
    if (!empty($name)) $updates[] = "name = ?";
    if (!empty($father_name)) $updates[] = "father_name = ?";
    if (!empty($mother_name)) $updates[] = "mother_name = ?";
    if (!empty($phone)) $updates[] = "phone = ?";
    if (!empty($email)) $updates[] = "email = ?";
    if (!is_null($age)) $updates[] = "age = ?";
    if (!empty($dob)) $updates[] = "dob = ?"; // Added DOB update logic
    if (!empty($gender)) $updates[] = "gender = ?";
    if (!empty($address)) $updates[] = "address = ?";
    if (!empty($category)) $updates[] = "category = ?";
    if (!empty($bloodgroup)) $updates[] = "bloodgroup = ?";
    if (!is_null($shift)) $updates[] = "shift = ?";
    if (!empty($course)) $updates[] = "course = ?";
    if (!is_null($unit)) $updates[] = "unit = ?";
    if (!empty($profilePhoto)) $updates[] = "profile_photo = ?";

    if (count($updates) > 0) {
        $sql = "UPDATE students SET " . implode(", ", $updates) . " WHERE register_no = ?";
        $stmt = $conn->prepare($sql);

        $params = [];
        if (!empty($name)) $params[] = $name;
        if (!empty($father_name)) $params[] = $father_name;
        if (!empty($mother_name)) $params[] = $mother_name;
        if (!empty($phone)) $params[] = $phone;
        if (!empty($email)) $params[] = $email;
        if (!is_null($age)) $params[] = $age;
        if (!empty($dob)) $params[] = $dob;
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
            echo "<script>alert('Student details updated successfully!'); window.location.href = 'manage_students.php';</script>";
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
        <b style="font-size: 1.3rem">Admin Portal</b><br>
    </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
    <ul>
        <li><a href="manage_applications.php">Manage Applications</a></li>
        <li><a class="active" href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_staff.php">Manage Staff</a></li>
        <li><a href="manage_announcements.php">Announcements</a></li>
        <li><a href="manage_more.php"> More</a></li>
        <li><a href="admin_logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  class="active" href="manage_students.php">View Admitted Students</a></li>
                <li><a  href="view_credit_application.php">View Credits Application</a></li>

                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
        </div>
        <div class="widget">
            <div class="mainapply">
                <h2>Modify Admitted Student Details</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="nss-form"  onsubmit="return validateForm();">
                    <label for="register_no">Register Number:</label>
                    <input type="text" id="register_no" name="register_no" value="<?= $student['register_no'] ?? '' ?>" required readonly>
                    

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?= $student['name'] ?? '' ?>"><br><br>

                    <label for="father_name">Father's Name:</label>
                    <input type="text" id="father_name" name="father_name" value="<?= $student['father_name'] ?? '' ?>"><br><br>

                    <label for="mother_name">Mother's Name:</label>
                    <input type="text" id="mother_name" name="mother_name" value="<?= $student['mother_name'] ?? '' ?>"><br><br>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?= $student['phone'] ?? '' ?>"><br><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $student['email'] ?? '' ?>"><br><br>
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?= $student['dob'] ?? '' ?>"><br><br>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age"  value="<?= $student['age'] ?? '' ?>"><br><br>

                    <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="" disabled>Select</option>
            <option value="MALE" <?= ($student['gender'] === 'MALE') ? 'selected' : '' ?>>Male</option>
            <option value="FEMALE" <?= ($student['gender'] === 'FEMALE') ? 'selected' : '' ?>>Female</option>
            <option value="OTHER" <?= ($student['gender'] === 'OTHER') ? 'selected' : '' ?>>Other</option>
        </select><br><br>
                    <label for="address">Address:</label>
                    <textarea id="address" name="address"><?= $student['address'] ?? '' ?></textarea><br><br>

                    <label for="category">Category:</label>
                        <select id="category" name="category">
                            <option value="" disabled>Select</option>
                            <option value="GENERAL" <?= ($student['category'] === 'GENERAL') ? 'selected' : '' ?>>GENERAL</option>
                            <option value="OBC" <?= ($student['category'] === 'OBC') ? 'selected' : '' ?>>OBC</option>
                            <option value="SC" <?= ($student['category'] === 'SC') ? 'selected' : '' ?>>SC</option>
                            <option value="ST" <?= ($student['category'] === 'ST') ? 'selected' : '' ?>>ST</option>
                        </select><br><br>

                    <label for="bloodgroup">Select Blood group:</label>
                    <select id="bloodgroup" name="bloodgroup">
                        <option value="" disabled>Select</option>
                        <option value="A+" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'AB-' ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= isset($student['bloodgroup']) && $student['bloodgroup'] === 'O-' ? 'selected' : '' ?>>O-</option>
                    </select><br><br>

                    <label for="shift">Select Shift:</label>
                    <select id="shift" name="shift">
                        <option value="" disabled>Select</option>
                        <option value="1" <?= isset($student['shift']) && $student['shift'] === '1' ? 'selected' : '' ?>>Shift 1</option>
                        <option value="2" <?= isset($student['shift']) && $student['shift'] === '2' ? 'selected' : '' ?>>Shift 2</option>
                        <option value="3" <?= isset($student['shift']) && $student['shift'] === '3' ? 'selected' : '' ?>>Shift 3</option>
                    </select><br><br>

                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" value="<?= $student['course'] ?? '' ?>"><br><br>

                    <label for="unit">Select Unit:</label>
                    <select id="unit" name="unit">
                        <option value="" disabled>Select Unit</option>
                        <option value="1" <?= isset($student['unit']) && $student['unit'] == '1' ? 'selected' : '' ?>>Unit 1</option>
                        <option value="2" <?= isset($student['unit']) && $student['unit'] == '2' ? 'selected' : '' ?>>Unit 2</option>
                        <option value="3" <?= isset($student['unit']) && $student['unit'] == '3' ? 'selected' : '' ?>>Unit 3</option>
                        <option value="4" <?= isset($student['unit']) && $student['unit'] == '4' ? 'selected' : '' ?>>Unit 4</option>
                        <option value="5" <?= isset($student['unit']) && $student['unit'] == '5' ? 'selected' : '' ?>>Unit 5</option>
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
<script>
        function validateForm() {
            const nameRegex = /^[A-Za-z\s]+$/;
            const phoneRegex = /^[0-9]{10}$/;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            let name = document.getElementById("name").value;
            let father_name = document.getElementById("father_name").value;
            let mother_name = document.getElementById("mother_name").value;
            
            let phone = document.getElementById("phone").value;
            let email = document.getElementById("email").value;
            let dob = document.getElementById("dob").value;
            let course = document.getElementById("course").value;
            let age = document.getElementById("age").value;

             // Validate Name
            if (!nameRegex.test(name)) {
                alert("Name should contain only letters and spaces.");
                return false;
            }
           
            if (!nameRegex.test(father_name)) {
                alert("Father Name should contain only letters and spaces.");
                return false;
            }

            if (!nameRegex.test(mother_name)) {
                alert("Mother Name should contain only letters and spaces.");
                return false;
            }

            // Validate Phone Number
            if (!phoneRegex.test(phone)) {
                alert("Phone number must be exactly 10 digits.");
                return false;
            }

            // Validate Email
            if (!emailRegex.test(email)) {
                alert("Enter a valid email address.");
                return false;
            }
           
            // Validate Course Name
            if (!nameRegex.test(course)) {
                alert("Course name should contain only letters and spaces.");
                return false;
            }

            // Validate Age
            if (age < 17 || age > 50) {
                alert("Enter proper Age details.");
                return false;
            }
            if (dob) {
            let dobDate = new Date(dob);
            let today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            let monthDiff = today.getMonth() - dobDate.getMonth();
            let dayDiff = today.getDate() - dobDate.getDate();

            // Adjust age if birth date hasn't occurred yet this year
            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                age--;
            }

            if (age < 17 || age > 50) {
                alert("Enter proper DoB details.");
                return false;
            }
        } else {
            alert("Please enter your Date of Birth.");
            return false;
        }
            
            return true;
        }

       
    </script>
<script src="script.js"></script>
</body>
</html>
