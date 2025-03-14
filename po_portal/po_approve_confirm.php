<?php
session_start();

// Storing session variable
if(!$_SESSION['po_id'] || !$_SESSION['unit']){
    header("Location: ../login.html");
}            
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css">
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
<div class="logo-container">
        <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Program Officer Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a   href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a class="active" href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
    <div class="about_nav">
          <ul>
            <li><a class="active" href="po_approve_attendance.php">View Attendance</a></li>
            <li><a href="">###</a></li>
            <li><a href="">###</a></li>
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
          
            $po_id = $_SESSION['po_id'];
            $po_unit = $_SESSION['unit'];
            $event_id = intval($_SESSION['att_evt_id']);
            // Create a connection object
            $conn_attendance = new mysqli("localhost", "root", "", "nss_db");
            if($conn_attendance->connect_error){
                die("Connection failed: " . $conn_attendance->connect_error);
            }
            $stmt_attendance = $conn_attendance->prepare("SELECT attendance.register_no, students.name 
            FROM attendance 
            JOIN students ON attendance.register_no = students.user_id
            JOIN events ON attendance.event_id = events.event_id
            WHERE events.event_id = ? AND students.unit = ? AND attendance.status='PENDING'");
            $stmt_attendance->bind_param("is", $event_id, $po_unit);
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

<?php
if(isset($_POST['approve'])){
        $selectedStudents = $_POST['selected_students'] ?? [];
        if (empty($selectedStudents)) {
            echo "<script>alert('Please select at least one student.');</script>";
            exit;
        }

        $conn = new mysqli("sql12.freesqldatabase.com", "sql12767434", "fdUtRRwmJ9", "sql12767434");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        foreach ($selectedStudents as $reg_no) {
            $stmt_update = $conn->prepare("UPDATE attendance SET status = 'PO_APPROVED' WHERE register_no = ? AND event_id = ?");
            $stmt_update->bind_param("si", $reg_no, $event_id);
            $stmt_update->execute();
        }

        $stmt_update->close();
        $conn->close();

        // echo "<script>alert('Attendance approved successfully.');</script>";

        header("Location: po_approve_attendance.php");
        exit();
}
?>