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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['role']) && !empty($_POST['role'])) {
    $role = $_POST['role'];

    // Fetch staff data based on the selected role
    $stmt = $conn->prepare("SELECT name, phone, email, dob, gender, address, user_id, profile_photo, unit,role 
                            FROM staff 
                            WHERE role = ?");
    $stmt->bind_param("s", $role);
} else {
    // Fetch all staff members when no specific role is selected
    $stmt = $conn->prepare("SELECT name, phone, email, dob, gender, address, user_id, profile_photo, unit,role 
                            FROM staff");
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
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    /* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
}

table th {
    background-color: #007bff;
    color: white;
}

/* Add scroll for long content */
.table-container {
    overflow-x: auto; /* Enable horizontal scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    max-height: 400px; /* Set maximum height for the table container */
    max-width: 100%; /* Ensure it fits within the screen */
    border: 1px solid #ccc; /* Optional: Add a border to define the scrollable area */
    padding: 5px; /* Optional: Add padding for aesthetics */
    background-color: #f9f9f9; /* Optional: Light background color */
}

/* Styling for the last column (Address) */
table td:last-child {
    max-width: 200px; /* Set width for the Address column */
    white-space: nowrap; /* Prevent wrapping */
    overflow: hidden; /* Hide overflow */
    text-overflow: ellipsis; /* Add ellipsis (...) for long text */
}

/* Show full text on hover */
table td:hover:last-child {
    overflow: visible; /* Show full text on hover */
    white-space: normal; /* Allow wrapping on hover */
}

/* Styling for the search form */
.search_form {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Distribute space evenly */
    gap: 10px; /* Space between elements */
    padding: 10px;
    border: 1px solid #ccc; /* Border around the form */
    border-radius: 8px; /* Rounded corners */
    background-color: #f9f9f9;
    max-width: 600px; /* Restrict form width */
    margin: 20px auto; /* Center form on the page */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: subtle shadow */
}

.search_form h1 {
    font-size: 16px;
    margin: 0;
    flex: 1; /* Allow heading to take space */
}

.search_form label {
    font-size: 14px;
    margin-right: 10px;
}

.search_form select {
    padding: 5px;
    font-size: 14px;
    border: 1px solid #bbb;
    border-radius: 5px;
    background-color: #fff;
    flex: 1; /* Adjust dropdown width */
}

.search_form button {
    padding: 5px 15px;
    font-size: 14px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search_form button:hover {
    background-color: #0056b3;
}
/* Styling for the button */
.admit-buttons{
    display: inline-block; /* Makes it behave like a button */
    padding: 10px 20px; /* Padding inside the button */
    font-size: 16px; /* Text size */
    font-weight: bold; /* Bold text */
    color: #fff; /* White text color */
    background-color: #007bff; /* Blue background */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
}

.admit-buttons:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.admit-buttons:active {
    transform: scale(0.98); /* Slightly shrink on click */
    background-color: #003f7f; /* Even darker blue */
}

.admit-buttons:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Add a subtle blue glow */
}
</style>
   
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
            <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a class="active" href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>
            <li><a href="manage_more.php"> More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="about_main_divide">
            <div class="about_nav">
                <ul>
                    
                    <li><a class="active" href="manage_staff.php">PO & Executive Account</a></li>
                    <li><a  href="po_leave.php">PO leave</a></li> 
                    <li><a href="change_EXE_PO_password.php">Change PO & Executive Password</a></li>
                </ul>
            </div>
            <div class="widget">
                <div class="search_form">
                    <h5>Program Officer & Executive Accounts</h5>
                    <form method="post">
                        <label for="role">Select Role:</label>
                        <select name="role" id="role" required>
                            <option value="" disabled selected>Choose Role</option>
                            <option value="EXECUTIVE">Executive</option>
                            <option value="PO">Program Officer</option>
                        </select>
                        <button type="submit">View</button>
                    </form>
                </div>
                <form action="modify_po.php" method="POST" onsubmit="return validateSelection()">
                    <div class="table-container">
                        <?php if (!empty($results)): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Profile Photo</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Date of Birth</th>
                                        <th>Gender</th>
                                        <th>Address</th>
                                        <th>Unit</th>
                                        <th>User ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $row): ?>
                                        <tr>
                                            <td><input type="checkbox" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>"></td>
                                            <td>
                                                <?php if (!empty($row['profile_photo'])): ?>
                                                    <img src="../<?= htmlspecialchars($row['profile_photo']) ?>" alt="Profile Photo" style='width: 50px; height: 50px; object-fit: cover; border-radius: 20%;'>
                                                    <?php else: ?>
                                                    No Photo
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['role']) ?></td>
                                            <td><?= htmlspecialchars($row['phone']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['dob']) ?></td>
                                            <td><?= htmlspecialchars($row['gender']) ?></td>
                                            <td><?= htmlspecialchars($row['address']) ?></td>
                                            <td><?= htmlspecialchars($row['unit']) ?></td>
                                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                <h5>No results found.</h5>
            <?php endif; ?>
                        
                    </div><br>
                    <button type="submit" name="modify" class="admit-buttons">Modify</button>
               
                <button type="button" class="admit-buttons" onclick="redirectToPage()">Create New Account</button>
                </form>
<script>
    function redirectToPage() {
        window.location.href = "create_po_exe_account.php";
    }
</script>            </div>
        </div>
    </div>

    <script>
        function validateSelection() {
            const checkboxes = document.querySelectorAll('input[name="user_id"]:checked');
            if (checkboxes.length > 1) {
                alert("Please select only one staff to modify.");
                return false; // Prevent form submission
            }
            if (checkboxes.length === 0) {
                alert("Please select at least one staff to modify.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
<script src="script.js"></script>
</body>
</html>
