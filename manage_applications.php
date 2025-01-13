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
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href=".css">
    

    <style>
        /*search applications*/
.search-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}
.search-container input[type="text"] {
    width: 300px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.search-container button {
    padding: 10px 15px;
    margin-left: 10px;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}
.search-container button:hover {
    background-color: #0056b3;
}

         /*show_application*  && admit student/
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
        .delete-button {
            background-color: #f44336; /* Red background */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            border-radius: 4px;
        }
        /* Style for the delete button */
.delete-button {
    display: inline-block; /* Makes it behave like a button */
    padding: 10px 20px; /* Padding inside the button */
    font-size: 16px; /* Text size */
    font-weight: bold; /* Bold text */
    color: #fff; /* White text color */
    background-color: #dc3545; /* Red background color */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
}

.delete-button:hover {
    background-color: #a71d2a; /* Darker red on hover */
}

.delete-button:active {
    transform: scale(0.98); /* Slightly shrink on click */
    background-color: #7f1521; /* Even darker red */
}

.delete-button:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.5); /* Add a subtle red glow */
}
/* Styling for the "Select All" button */
.select-all-button {
    display: inline-block; /* Makes it behave like a button */
    padding: 10px 20px; /* Padding inside the button */
    font-size: 16px; /* Text size */
    font-weight: bold; /* Bold text */
    color: #fff; /* White text color */
    background-color: #6c757d; /* Grey background */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
}

.select-all-button:hover {
    background-color: #5a6268; /* Darker grey on hover */
}

.select-all-button:active {
    transform: scale(0.98); /* Slightly shrink on click */
    background-color: #4e555b; /* Even darker grey */
}

.select-all-button:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 5px rgba(108, 117, 125, 0.5); /* Add a subtle grey glow */
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
            <li><a  href="view_admitted_students.php"> Manage Students</a></li>
            <li><a href="view_po.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
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
                        $conn = new mysqli("localhost", "root", "", "nss_application");

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
                                    <td><input type='checkbox' name='selected_students[]' value='{$row['Register_no']}'></td>
                                    <td>{$row['Register_no']}</td>
                                    <td>{$row['Name']}</td>
                                    <td>{$row['Phone']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>{$row['Age']}</td>
                                    <td>{$row['Bloodgroup']}</td>
                                    <td>{$row['Shift']}</td>
                                    <td>{$row['Gender']}</td>
                                    <td>{$row['Course']}</td>
                                    <td>{$row['Address']}</td>
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

            $conn = new mysqli("localhost", "root", "", "nss_application");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            foreach ($selectedStudents as $register_no) {
                $sql = "SELECT * FROM applications WHERE Register_no = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $register_no);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $student = $result->fetch_assoc();
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
                        $student['Register_no'], 
                        $student['Register_no'], 
                        $commonUnit
                    );

                    if ($insertStmt->execute()) {
                        $deleteSQL = "DELETE FROM applications WHERE Register_no = ?";
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

            $conn = new mysqli("localhost", "root", "", "nss_application");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            foreach ($selectedStudents as $register_no) {
                $deleteSQL = "DELETE FROM applications WHERE Register_no = ?";
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
</body>
</html>
