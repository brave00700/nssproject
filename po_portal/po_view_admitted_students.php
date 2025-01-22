<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['po_id']) || !isset($_SESSION['unit'])) {
    header("Location: ../login.html");
    exit();
}

// Retrieve officer's unit from the session
$officerUnit = intval($_SESSION['unit']);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_application";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$searchResults = [];

// Handle student search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $searchQuery = "SELECT * FROM admitted_students WHERE Unit = ? AND register_no LIKE ?";
    $stmt = $conn->prepare($searchQuery);
    $searchPattern = "%$search%";
    $stmt->bind_param("is", $officerUnit, $searchPattern);
    $stmt->execute();
    $searchResult = $stmt->get_result();

    while ($row = $searchResult->fetch_assoc()) {
        $searchResults[] = $row;
    }

    $stmt->close();
} else {
    // Display all students from the officer's unit by default
    $query = "SELECT * FROM admitted_students WHERE Unit = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $officerUnit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Officer - Manage Students</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style.css">
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
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b><br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
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
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a class="active" href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a href=".php"> ####</a></li>
            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>

<div class="main">
    <div class="special_widget">
        
            <form class="search-container" method="POST">
                
                <input type="text" id="search" name="search" placeholder="Enter Register Number" required>
                <button type="submit">Search</button>
            </form>
        
        <form  method="POST">
        <div class="table-container">
            <?php if (!empty($searchResults)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>
                        <th>Profile Photo</th>
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
                        <th>Blood Group</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($searchResults as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="register_no[]" value="<?= htmlspecialchars($row['Register_no']) ?>"></td>
                            <td>
                                <?php if (!empty($row['ProfilePhoto'])): ?>
                                    <img src="<?= htmlspecialchars($row['ProfilePhoto']) ?>" alt="Profile Photo" style='width: 50px; height: 50px; object-fit: cover; border-radius: 20%;'>
                                <?php else: ?>
                                    No Photo
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['Register_no']) ?></td>
                            <td><?= htmlspecialchars($row['Unit']) ?></td>
                            <td><?= htmlspecialchars($row['Name']) ?></td>
                            <td><?= htmlspecialchars($row['Course']) ?></td>
                            <td><?= htmlspecialchars($row['Shift']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['Phone']) ?></td>
                            <td><?= htmlspecialchars($row['Father_name']) ?></td>
                            <td><?= htmlspecialchars($row['Mother_name']) ?></td>
                            <td><?= htmlspecialchars($row['Age']) ?></td>
                            <td><?= htmlspecialchars($row['Gender']) ?></td>
                            <td><?= htmlspecialchars($row['Address']) ?></td>
                            <td><?= htmlspecialchars($row['Category']) ?></td>
                            <td><?= htmlspecialchars($row['Bloodgroup']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h5>No students found.</h5>
            <?php endif; ?>
        </div>
        <button type="submit" formaction="po_modify_std.php" name="modify" class="admit-buttons" onclick="return validateSelection()" >Modify</button>
        <button type="submit" formaction="view_report.php" name="view" class="admit-buttons" >View Report</button>
        
    </form>
    </div>
</div>
</body>
</html>
