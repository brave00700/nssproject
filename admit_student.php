<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: login.html");
}            ?>
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

/* Table container with scroll*/
.table-container {
    overflow-x: auto; /* Enable horizontal scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    max-height: 400px; /* Set the maximum height for the table container */
    max-width: 100%; /* Ensure it fits within the screen */
    border: 1px solid #ccc; /* Optional: Add a border to define the scrollable area */
    padding: 5px; /* Optional: Add padding for aesthetics */
    background-color: #f9f9f9; /* Optional: Light background color */
}

/* Style for both horizontal and vertical scrollbars */
.table-container::-webkit-scrollbar {
    width: 8px; /* Set the width for both horizontal and vertical scrollbars */
    height: 8px; /* Set the height for horizontal scrollbar */
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1; /* Background color of the scrollbar track */
}

.table-container::-webkit-scrollbar-thumb {
    background: #888; /* Color of the scrollbar thumb */
    border-radius: 4px; /* Rounded corners for the thumb */
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555; /* Darker color on hover */
}


    /* Styling for the select dropdown */
select {
    width: 100%; /* Full width */
    padding: 10px; /* Padding inside the dropdown */
    margin: 10px 0; /* Space around the dropdown */
    border: 1px solid #ccc; /* Light gray border */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px; /* Larger font size */
    background-color: #f9f9f9; /* Light background color */
    color: #333; /* Text color */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    transition: all 0.3s ease; /* Smooth transitions */
}

select:hover {
    border-color: #007bff; /* Change border color on hover */
    background-color: #fff; /* Slightly brighter background */
}

select:focus {
    outline: none; /* Remove default outline */
    border-color: #007bff; /* Highlighted border */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Blue glow */
}

/* Styling for the button */
button[type="submit"] {
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

button[type="submit"]:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

button[type="submit"]:active {
    transform: scale(0.98); /* Slightly shrink on click */
    background-color: #003f7f; /* Even darker blue */
}

button[type="submit"]:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Add a subtle blue glow */
}

    </style>
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b><br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
        <b style="font-size: 1.3rem">Admin Portal</b><br>
    </h1>
    <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    <ul>
            <li><a class="active" href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
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
                                <th>Course</th>
                                <th>Shift</th>
                                <th>Father's Name</th>
                                <th>Mother's Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Age</th>
                                <th>Address</th>
                                <th>Gender</th>
                                <th>Category</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "nss_application");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $sql = "SELECT * FROM applications";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_students[]' value='{$row['Register_no']}'></td>
                                        <td>{$row['Register_no']}</td>
                                        <td>{$row['Name']}</td>
                                        <td>{$row['Course']}</td>
                                        <td>{$row['Shift']}</td>
                                        <td>{$row['Father_name']}</td>
                                        <td>{$row['Mother_name']}</td>
                                        <td>{$row['Phone']}</td>
                                        <td>{$row['Email']}</td>
                                        <td>{$row['Age']}</td>
                                        <td>{$row['Address']}</td>
                                        <td>{$row['Gender']}</td>
                                        <td>{$row['Category']}</td>
                                        
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13'>No applications found</td></tr>";
                        }
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <label for="common_unit">Select Unit:</label>
                    <select id="common_unit" name="common_unit" required>
                        <option value="" disabled selected>Select Unit</option>
                        <option value="1">Unit 1</option>
                        <option value="2">Unit 2</option>
                        <option value="3">Unit 3</option>
                        <option value="4">Unit 4</option>
                        <option value="5">Unit 5</option>
                    </select>
                </div>
                <button type="submit" name="admit">Admit Selected Students</button>
            </form>
        </div>

        <?php
if (isset($_POST['admit'])) {
    $selectedStudents = $_POST['selected_students'] ?? [];
    $commonUnit = $_POST['common_unit'] ?? null;

    if (empty($selectedStudents)) {
        echo "<script>alert('Please select at least one student.');</script>";
        exit;
    }

    if (empty($commonUnit)) {
        echo "<script>alert('Please select a unit.');</script>";
        exit;
    }

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "nss_application");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($selectedStudents as $register_no) {
        // Fetch student details from the `applications` table
        $sql = "SELECT * FROM applications WHERE Register_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $register_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();

            // Insert student details into the `admitted_students` table
            $insertSQL = "INSERT INTO admitted_students
                          (Register_no, Name, Father_name, Mother_name, Phone, Email, Age, Gender, Address, Category, Bloodgroup, Shift, Course, ProfilePhoto, user_id, password, Unit) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
                $student['Register_no'], // user_id
                $student['Register_no'], // password
                $commonUnit
            );

            if ($insertStmt->execute()) {
                // If insertion is successful, delete the student from the `applications` table
                $deleteSQL = "DELETE FROM applications WHERE Register_no = ?";
                $deleteStmt = $conn->prepare($deleteSQL);
                $deleteStmt->bind_param("s", $register_no);
                $deleteStmt->execute();

                echo "<script>console.log('Student with Register No {$student['Register_no']} admitted successfully.');</script>";
            } else {
                echo "<script>alert('Error admitting student with Register No {$student['Register_no']}: {$insertStmt->error}');</script>";
            }
        } else {
            echo "<script>alert('No student found with Register No {$register_no}.');</script>";
        }

        $stmt->close();
    }

    echo "<script>alert('Selected students admitted successfully!');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
    $conn->close();
}
?>


       
    </div>
</div>
</body>
</html>
