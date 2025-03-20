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

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}            ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/manage_application.css">

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
        <li><a class="active" href="manage_applications.php">Manage Applications</a></li>
            <li><a  href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>
                        <li><a href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

<div class="main">
    
        <div class="special_widget">
            

            <!-- Search Form -->
            <div class="search-container">
                <form method="POST" action="">
                    <input type="text" name="search" placeholder="Enter Register Number" required>
                    <button type="submit">Search</button>
                </form>
            </div>

            <!-- Student Management Form -->
            <form method="post" action="">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Register Number</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Age</th>
                                <th>Blood Group</th>
                                <th>Shift</th>
                                <th>Gender</th>
                                <th>Course</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $query = "SELECT * FROM applications";
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && !empty($_POST['search'])) {
                            $search = $conn->real_escape_string($_POST['search']);
                            $query = "SELECT * FROM applications WHERE Register_no LIKE '%$search%'";
                        }

                        $result = $conn->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td><input type='checkbox' name='selected_students[]' value='{$row['register_no']}'></td>
                                    <td>{$row['register_no']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['age']}</td>
                                    <td>{$row['bloodgroup']}</td>
                                    <td>{$row['shift']}</td>
                                    <td>{$row['gender']}</td>
                                    <td>{$row['course']}</td>
                                    <td>{$row['address']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No results found</td></tr>";
                        }
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>

                <!-- Unit Selection -->
                <div>
                    <label for="common_unit">Select Unit:</label>
                    <select id="common_unit" name="common_unit" >
                        <option value="" disabled selected>Select Unit</option>
                        <option value="1">Unit 1</option>
                        <option value="2">Unit 2</option>
                        <option value="3">Unit 3</option>
                        <option value="4">Unit 4</option>
                        <option value="5">Unit 5</option>
                    </select>
                </div>

                <div >
                    <button type="button" class="select-all-button" id="select-all-button" style="background:">Select All</button>
                    <button type="submit" name="admit" class="admit-buttons" >Admit Selected Students</button>
                    <button type="submit" name="delete" class="delete-button">Delete Selected Students</button>
                </div>
            </form>
        </div>

        <?php
        // Admit students logic
        if (isset($_POST['admit'])) {
            $selectedStudents = $_POST['selected_students'] ?? [];
            $commonUnit = $_POST['common_unit'] ?? null;
             
            if (empty($_POST['common_unit'])) {
                echo "<script>alert('Please select unit.');</script>";
                exit;
            }
            if (empty($selectedStudents)) {
                echo "<script>alert('Please select at least one student.');</script>";
                exit;
            }

            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            foreach ($selectedStudents as $register_no) {
                $sql = "SELECT * FROM applications WHERE register_no = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $register_no);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $student = $result->fetch_assoc();
                    $hashedPassword = password_hash($student['register_no'], PASSWORD_DEFAULT);
                    $insertSQL = "INSERT INTO students
                                  (register_no, name, father_name, mother_name, phone, email, age, dob, gender, address, category, bloodgroup, shift, course, profile_photo, user_id, password, unit) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $insertStmt = $conn->prepare($insertSQL);
                    $insertStmt->bind_param(
                        "ssssssissssssssssi",
                        $student['register_no'],
                        $student['name'],
                        $student['father_name'],
                        $student['mother_name'],
                        $student['phone'],
                        $student['email'],
                        $student['age'],
                        $student['dob'],
                        $student['gender'],
                        $student['address'],
                        $student['category'],
                        $student['bloodgroup'],
                        $student['shift'],
                        $student['course'],
                        $student['profile_photo'],
                        $student['register_no'], 
                        $hashedPassword, 
                        $commonUnit
                    );

                    if ($insertStmt->execute()) {
                        $deleteSQL = "DELETE FROM applications WHERE register_no = ?";
                        $deleteStmt = $conn->prepare($deleteSQL);
                        $deleteStmt->bind_param("s", $register_no);
                        $deleteStmt->execute();
                    }
                }

                $stmt->close();
            }

            echo "<script>alert('Selected students admitted successfully!');</script>";
            echo "<script>window.location.href = window.location.href;</script>";
            $conn->close();
        }

        // Delete students logic
        if (isset($_POST['delete'])) {
            $selectedStudents = $_POST['selected_students'] ?? [];

            if (empty($selectedStudents)) {
                echo "<script>alert('Please select at least one student to delete.');</script>";
                exit;
            }

            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            foreach ($selectedStudents as $register_no) {
                $deleteSQL = "DELETE FROM applications WHERE register_no = ?";
                $deleteStmt = $conn->prepare($deleteSQL);
                $deleteStmt->bind_param("s", $register_no);
                $deleteStmt->execute();
            }

            echo "<script>alert('Selected students deleted successfully!');</script>";
            echo "<script>window.location.href = window.location.href;</script>";
            $conn->close();
        }
        ?>
    </div>
</div>
<script>
    // JavaScript to handle "Select All" functionality
    document.getElementById("select-all-button").addEventListener("click", function () {
        // Get all the checkboxes
        const checkboxes = document.querySelectorAll("input[type='checkbox'][name='selected_students[]']");
        // Toggle the selection of all checkboxes
        const isChecked = Array.from(checkboxes).some(checkbox => !checkbox.checked);
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });
</script>
<script src="script.js"></script>
</body>
</html>
