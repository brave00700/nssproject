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

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted for updating units
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_unit'], $_POST['register_no'])) {
    $selectedStudents = $_POST['register_no'];
    $newUnit = intval($_POST['new_unit']);

    // Validate input
    if (empty($selectedStudents) || $newUnit < 1 || $newUnit > 5) {
        echo "<script>alert('Invalid data. Please try again.'); window.location.href = 'view_admitted_students.php';</script>";
        exit();
    }

    // Update units for selected students
    $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
    $sql = "UPDATE students SET Unit = ? WHERE register_no IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    // Dynamically bind parameters
    $types = str_repeat('s', count($selectedStudents)); // 's' for each student ID
    $stmt->bind_param("i" . $types, $newUnit, ...$selectedStudents);

    if ($stmt->execute()) {
        echo "<script>alert('Unit updated successfully for selected students.'); window.location.href = 'manage_students.php';</script>";
    } else {
        echo "<script>alert('Error updating unit. Please try again.'); window.location.href = 'manage_students.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit();
}

// If students are selected for unit change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_no'])) {
    $selectedStudents = $_POST['register_no'];

    // Get details of selected students
    $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
    $sql = "SELECT register_no, name, unit FROM students WHERE register_no IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($selectedStudents)), ...$selectedStudents);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();
} else {
    // Redirect back if no students were selected
    header("Location: view_admitted_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/manage_student.css">

        

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
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                                    <li><a href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>



<div class="main">
<div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  class="active" href="manage_students.php">Admitted Students</a></li>
                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a  href="view_credit_application.php">Credits Application</a></li>
                
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
        </div><div class="widget">
<div class="main-container">
    <h1>Change Unit for Selected Students</h1>
    <form action="" method="POST">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Current Unit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['register_no']) ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= htmlspecialchars($student['unit']) ?></td>
                </tr>
                <!-- Hidden input to pass student register numbers -->
                <input type="hidden" name="register_no[]" value="<?= htmlspecialchars($student['register_no']) ?>">
            <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <label for="unit">Select New Unit:</label>
        <select name="new_unit" id="unit" required>
            <option value="" disabled selected>Select Unit</option>
            <option value="1">Unit 1</option>
            <option value="2">Unit 2</option>
            <option value="3">Unit 3</option>
            <option value="4">Unit 4</option>
            <option value="5">Unit 5</option>
        </select>
        <br><br>
        <div class="button-container">
        <button type="submit" class="submit-button">Update Unit</button>
        <a href="manage_students.php" class="cancel-button">Cancel</a>
        </div>

    </form>
    </div>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
