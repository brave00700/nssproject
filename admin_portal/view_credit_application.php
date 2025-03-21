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

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$results = [];

// Handle filtering and searching
$whereClauses = [];
$params = [];
$types = "";

// Filter by status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['status']) && $_POST['status'] !== "") {
    $whereClauses[] = "status = ?";
    $params[] = $_POST['status'];
    $types .= "s";
}

// Search by register number
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_no']) && !empty($_POST['register_no'])) {
    $whereClauses[] = "register_no = ?";
    $params[] = $_POST['register_no'];
    $types .= "s";
}

// Build query dynamically
$query = "SELECT credit_id, register_no, credits, status FROM credits";
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Credit Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/view_credit_application.css">
    <style>
       
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
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                        <li><a  href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>
<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            
            <ul>
                <li><a href="manage_students.php">Admitted Students</a></li>
                <li><a href="admin_approve_attendance.php">Approve Attendance</a></li>

                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a class="active"  href="view_credit_application.php">Credits Application</a></li>
                <li><a href="change_student_password.php">Change Student Password</a></li>
            
            
            </ul>
        </div>
        <div class="widget">
<h1 style="text-align:center;">View Credit Applications</h1>
<div class="search_form_container">
<!-- Search by Status Form -->
<div class="search_form">
    <form method="post">
        <label for="status">Filter by Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="APPROVED">Approved</option>
            <option value="PENDING">Pending</option>
            <option value="PO_APPROVED">PO Approved</option>
            <option value="REJECTED">Rejected</option>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<!-- Search by Register No Form -->
<div class="search_form">
    <form method="post">
        <label for="register_no">Search by Register No:</label>
        <input type="text" name="register_no" id="register_no" placeholder="Enter Register No">
        <button type="submit">Search</button>
    </form>
</div>
</div>
<!-- Display Credit Applications -->
<form method="POST" onsubmit="return validateSelection()">
    <div class="table-container">
        <?php if (!empty($results)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Select</th>  
                        <th>Credit ID</th>    
                        <th>Register No</th>
                        <th>Credits</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="credit_id" value="<?= htmlspecialchars($row['credit_id']) ?>"></td>
                            <td><?= htmlspecialchars($row['credit_id']) ?></td>
                            <td><?= htmlspecialchars($row['register_no']) ?></td>
                            <td><?= htmlspecialchars($row['credits']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <p>No applications found.</p>
        <?php endif; ?>
    </div>
    <br>
    <button type="submit" formaction="view_report_approve.php" name="modify" class="admit-buttons">View Report and Approve</button>
    
</form>

<script>
     function validateSelection() {
            const checkboxes = document.querySelectorAll('input[name="credit_id"]:checked');
            if (checkboxes.length > 1) {
                alert("Please select only one application .");
                return false; // Prevent form submission
            }
            if (checkboxes.length === 0) {
                alert("Please select at least one application .");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
</script>
</div>
</div>
</div>
<script src="script.js"></script>
</body>
</html>


