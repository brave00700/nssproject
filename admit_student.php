<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminpotal.css">
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
        max-width: 100%; /* Ensure it fits within the screen */
    }
    table td:last-child {
        max-width: 200px; /* Set width for the Address column */
        white-space: nowrap; /* Prevent wrapping */
        overflow: hidden; /* Hide overflow */
        text-overflow: ellipsis; /* Add ellipsis (...) for long text */
    }
    table td:hover:last-child {
        overflow: visible; /* Show full text on hover */
        white-space: normal; /* Allow wrapping on hover */
    }
</style>
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Admin Portal</b><br>
        </h1> 
        <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active"  href="manage_announcements.php">Manage Announcements</a></li>
            <li><a href="manage_passwords.php">Manage Passwords</a></li>
            <li><a href="">####</a></li>
            <li><a href="">####</a></li>
        </ul>
    </div>
    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="show_applications.php">Show All Applications</a></li>
            <li><a href="search_applications.php">Search Applications</a></li>
            <li><a href="delete_applications.php">Delete Applications</a></li>
            <li><a class="active" href="admit_student.php">Admit Students</a></li>
          </ul>
        </div>
        <div class="widget">
        <h1>Admit Students to NSS Units</h1>
    <form method="post" action="">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Register Number</th>
                        <th>Name</th>
                        <th>Father's Name</th>
                        <th>Mother's Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>Category</th>
                        <th>Course</th>
                        <th>Shift</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "nss_application");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch data from applications table
                $sql = "SELECT * FROM applications";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td><input type='checkbox' name='selected_students[]' value='{$row['Register_no']}'></td>
                                <td>{$row['Register_no']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Father_name']}</td>
                                <td>{$row['Mother_name']}</td>
                                <td>{$row['Phone']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Age']}</td>
                                <td>{$row['Address']}</td>
                                <td>{$row['Gender']}</td>
                                <td>{$row['Category']}</td>
                                <td>{$row['Course']}</td>
                                <td>{$row['Shift']}</td>
                                <td>
                                    <select name='unit[{$row['Register_no']}]'>
                                        <option value='' disabled selected>Select Unit</option>
                                        <option value='1'>Unit 1</option>
                                        <option value='2'>Unit 2</option>
                                        <option value='3'>Unit 3</option>
                                        <option value='4'>Unit 4</option>
                                        <option value='5'>Unit 5</option>
                                    </select>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No applications found</td></tr>";
                }

                $conn->close();
                ?>
                </tbody>
            </table>
        </div>
        <button type="submit" name="admit">Admit Selected Students</button>
    </form>
</div>

<?php
if (isset($_POST['admit'])) {
    $selectedStudents = $_POST['selected_students'] ?? [];
    $units = $_POST['unit'] ?? [];

    if (empty($selectedStudents)) {
        echo "<script>alert('Please select at least one student.');</script>";
        exit;
    }

    // Reconnect to the database
    $conn = new mysqli("localhost", "root", "", "nss_application");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($selectedStudents as $register_no) {
        $unit = $units[$register_no] ?? null;

        if (!$unit) {
            echo "<script>alert('Please assign a unit for all selected students.');</script>";
            exit;
        }

        // Fetch student details
        $sql = "SELECT * FROM applications WHERE Register_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $register_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();

            // Insert into admitted_students table
            $insertSQL = "INSERT INTO admitted_students
                          (Register_no, Name, Father_name, Mother_name, Phone, Email, Age, Gender, Address, Category, Bloodgroup, Shift, Course, ProfilePhoto,user_id,password, Unit) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
            $insertStmt = $conn->prepare($insertSQL);
            $insertStmt->bind_param(
                "ssssssisssssssssi",
                $student['Register_no'],
                $student['Name'],
                $student['Father_name'],
                $student['Mother_name'],
                $student['Phone'],
                $student['Email'],
                $student['Age'],
                $student['Gender'],
                $student['Address'],
                $student['Category'],
                $student['Bloodgroup'],
                $student['Shift'],
                $student['Course'],
                $student['ProfilePhoto'],
                $student['Register_no'],
                $student['Register_no'],
                $unit
            );

            if (!$insertStmt->execute()) {
                echo "<script>alert('Error admitting student: {$insertStmt->error}');</script>";
            }
        }
    }

    echo "<script>alert('Selected students admitted successfully!');</script>";
    $conn->close();
}
?>

        </div>
    </div>
</div>
</body>
</html>

   
