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

// Initialize variables
$searchResults = [];
$unitResults = [];

// Handle student search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $searchQuery = "SELECT * FROM students WHERE register_no LIKE '%$search%'";
    $searchResult = $conn->query($searchQuery);

    if ($searchResult->num_rows > 0) {
        while ($row = $searchResult->fetch_assoc()) {
            $searchResults[] = $row;
        }
    } else {
        echo "<script>alert('No results found.');</script>";
    }
}

// Handle unit-wise view
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['unit'])) {
    $unit = intval($_POST['unit']);
    $stmt = $conn->prepare("SELECT * FROM students WHERE unit = ?");
    $stmt->bind_param("i", $unit);
    $stmt->execute();
    $unitResult = $stmt->get_result();

    while ($row = $unitResult->fetch_assoc()) {
        $unitResults[] = $row;
    }

    $stmt->close();
}
//Fetch all students if no search or filter is applied

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $allStudentsQuery = "SELECT * FROM students";
    $allStudentsResult = $conn->query($allStudentsQuery);

    if ($allStudentsResult->num_rows > 0) {
        while ($row = $allStudentsResult->fetch_assoc()) {
            $searchResults[] = $row;
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
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/manage_student.css">

    <style>
    /* Table styling */


    </style>
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
                <li><a href="admin_approve_attendance.php">Approve Attendance</a></li>

                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a  href="view_credit_application.php">Credits Application</a></li>
                
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
        </div><div class="widget">
            
        <div class="search_form_container">
    <form class="search_form" method="POST">
        <label for="search">Search by Register No:</label>
        <input type="text" id="search" name="search" placeholder="Enter Register Number" required>
        <button type="submit">Search</button>
    </form>

    <form class="search_form" method="POST">
        <label for="unit">View Students Unit-Wise:</label>
        <select name="unit" id="unit" required>
            <option value="" disabled selected>Select Unit</option>
            <option value="1">Unit 1</option>
            <option value="2">Unit 2</option>
            <option value="3">Unit 3</option>
            <option value="4">Unit 4</option>
            <option value="5">Unit 5</option>
        </select>
        <button type="submit">View</button>
    </form>
    </div>
    <form  method="POST">
        <div class="table-container">
            <?php if (!empty($searchResults) || !empty($unitResults)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>
                        <th>ProfilePhoto</th>
                        <th>Register Number</th>
                        <th>Unit</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Shift</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Category</th>
                        <th>Bloodgroup</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_merge($searchResults, $unitResults) as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="register_no[]" value="<?= htmlspecialchars($row['register_no']) ?>" ></td>
                            <td>
                                <?php if (!empty($row['profile_photo'])): ?>
                                    <img src="..<?= htmlspecialchars($row['profile_photo']) ?>" alt="Profile Photo" style='width: 50px; height: 50px; object-fit: cover; border-radius: 20%;'>
                                    <?php else: ?>
                                    No Photo
                                <?php endif; ?>
                            </td>
                            
                            <td><?= htmlspecialchars($row['register_no']) ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars($row['shift']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['father_name']) ?></td>
                            <td><?= htmlspecialchars($row['mother_name']) ?></td>
                            <td><?= htmlspecialchars($row['age']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= htmlspecialchars($row['bloodgroup']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php else: ?>
                <h5>No students found.</h5>
            <?php endif; ?>
        </div><br>
        <button type="submit" formaction="modify_std.php" name="modify" class="admit-buttons" onclick="return validateSelection()" >Modify</button>
        <button type="submit" formaction="view_report.php" name="view" class="admit-buttons" onclick="return validateSelection()" >View Report</button>
        <button type="submit" formaction="change_unit.php"name="change_unit" class="admit-buttons" onclick="return SelectAtLeastOne()">Change Unit</button>
    </form>
    <script>
    function validateSelection() {
        const checkboxes = document.querySelectorAll('input[name="register_no[]"]:checked');
        if (checkboxes.length > 1) {
            alert("Please select only one student to modify.");
            return false; // Prevent form submission
        }
        if (checkboxes.length  === 0) {
            alert("Please select at least one student to modify.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
    function SelectAtLeastOne() {
        const checkboxes = document.querySelectorAll('input[name="register_no[]"]:checked');
        if (checkboxes.length  === 0) {
            alert("Please select at least one student to modify.");
            return false; // Prevent form submission
        }
        return true; 
    }
</script>
</div>
</div>
</div>
</div>

<script src="script.js"></script>
</body>
</html>
