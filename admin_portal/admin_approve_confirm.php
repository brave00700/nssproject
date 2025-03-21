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

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}   
if(isset($_POST['approve'])){
        $selectedStudents = $_POST['selected_students'] ?? [];
        if (empty($selectedStudents)) {
            echo "<script>alert('Please select at least one student.');  window.location.href='admin_approve_confirm.php';</script>";
            exit;
        }

        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        foreach ($selectedStudents as $reg_no) {
            $stmt_update = $conn->prepare("UPDATE attendance SET status = 'APPROVED' WHERE register_no = ? AND event_id = ?");
            $stmt_update->bind_param("si", $reg_no, $event_id);
            $stmt_update->execute();
        }

        $stmt_update->close();
        $conn->close();

        echo "<script>alert('Attendance approved successfully.');</script>";

        header("Location: admin_approve_attendance.php");
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

    <style>
        .widget {
            width: 100%;
        }
        .styled-table {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 1rem;
    font-family: Arial, sans-serif;
    min-width: 400px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    width: 100%;
}

.styled-table tbody tr:first-child {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
}

.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:nth-of-type(odd):not(:first-child) {
    background-color: #ffffff;
}


.styled-table tbody tr:hover:not(:first-child) {
    background-color: #f1f1f1;
}

.styled-table button {
    background-color: #009879;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.styled-table button:hover {
    background-color: #007b63;
}

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
                <li><a   href="manage_students.php">Admitted Students</a></li>
                <li><a class="active" href="admin_approve_attendance.php">Approve Attendance</a></li>
                <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a  href="view_credit_application.php">Credits Application</a></li>
                
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
            </div>

        <div class="widget">
            <form method="POST">
            <table class="styled-table">
                <tr>
                    <td><input type="checkbox" class="select-all"></td>
                    <td>Register No</td>
                    <td>Name</td>
                </tr>
            <?php
          
            
            $event_id = intval($_SESSION['att_evt_id']);
            // Create a connection object
            $conn_attendance = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if($conn_attendance->connect_error){
                die("Connection failed: " . $conn_attendance->connect_error);
            }
            $stmt_attendance = $conn_attendance->prepare("SELECT attendance.register_no, students.name 
            FROM attendance 
            JOIN students ON attendance.register_no = students.user_id
            JOIN events ON attendance.event_id = events.event_id
            WHERE events.event_id = ? AND attendance.status='PO_APPROVED'");
            $stmt_attendance->bind_param("i", $event_id);
            $stmt_attendance->execute();
            $result = $stmt_attendance->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td><input type='checkbox' name='selected_students[]' value='{$row['register_no']}'></td>
                        <td>{$row['register_no']}</td>
                        <td>{$row['name']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No results found</td></tr>";
            }
            $stmt_attendance->close();
            $conn_attendance->close();
            ?>
            </table>
            <button type="submit" name="approve">Approve</button>
        </form>
        </div>
    </div>
</div>
<script>
    let checkbox = document.querySelector('.select-all');
    let checkboxes = document.querySelectorAll('input[type="checkbox"]')
    checkbox.addEventListener("change",() => {
        if(checkbox.checked){
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        }else{
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        }
    })
</script>
<script src="script.js"></script>
</body>
</html>