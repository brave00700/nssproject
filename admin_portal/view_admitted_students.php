<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}

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
$unitResults = [];

// Handle student search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $searchQuery = "SELECT * FROM admitted_students WHERE register_no LIKE '%$search%'";
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
    $stmt = $conn->prepare("SELECT * FROM admitted_students WHERE Unit = ?");
    $stmt->bind_param("i", $unit);
    $stmt->execute();
    $unitResult = $stmt->get_result();

    while ($row = $unitResult->fetch_assoc()) {
        $unitResults[] = $row;
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
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style.css">
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

/* Container for both forms */
.search_form_container {
    display: flex;
    align-items: center; /* Vertically align forms */
    justify-content: space-between; /* Space forms evenly */
    gap: 20px; /* Space between the two forms */
    flex-wrap: wrap; /* Allow wrapping on small screens */
    max-width: 100%; /* Ensure the container fits the screen */
    margin: 10px auto; /* Center on the page */
}

/* Individual form styling */
.search_form {
    display: flex;
    align-items: center; /* Vertically align items */
    gap: 8px; /* Space between items */
    padding: 5px; /* Reduced padding for compactness */
    background-color: #f9f9f9; /* Light background color */
    border: 1px solid #ddd; /* Subtle border */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    flex: 1; /* Allow forms to share space equally */
    min-width: 300px; /* Ensure a minimum width for forms */
}

/* Labels inside the form */
.search_form label {
    font-weight: bold; /* Emphasize labels */
    font-size: 14px; /* Standard font size */
    margin-right: 5px; /* Add space to the right */
}

/* Input fields and dropdown */
.search_form input,
.search_form select {
    padding: 5px; /* Reduced padding */
    font-size: 14px; /* Consistent font size */
    border: 1px solid #ccc; /* Border for input fields */
    border-radius: 3px; /* Rounded edges */
    flex: 1; /* Allow fields to stretch */
    max-width: 200px; /* Restrict maximum width */
}

/* Buttons */
.search_form button {
    padding: 5px 10px; /* Reduced padding */
    font-size: 14px; /* Consistent font size */
    background-color: #007bff; /* Primary blue */
    color: #ffffff; /* White text for contrast */
    border: none; /* Remove default border */
    border-radius: 3px; /* Rounded edges */
    cursor: pointer; /* Pointer cursor for button */
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

/* Hover effect for buttons */
.search_form button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

/* Adjust spacing on small screens */
@media (max-width: 768px) {
    .search_form_container {
        flex-direction: column; /* Stack forms vertically on small screens */
        gap: 10px; /* Reduce gap between forms */
    }

    .search_form {
        min-width: unset; /* Remove minimum width on smaller screens */
        width: 100%; /* Make forms full width */
    }
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
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b><br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
        <b style="font-size: 1.3rem">Admin Portal</b><br>
    </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="view_admitted_students.php"> Manage Students</a></li>
            <li><a href="view_po.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>



<div class="main">
<div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  class="active" href="view_admitted_students.php">View Admitted Students</a></li>
                
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
                            <td><input type="checkbox" name="register_no[]" value="<?= htmlspecialchars($row['Register_no']) ?>" ></td>
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
        </div><br>
        <button type="submit" formaction="modify_std.php" name="modify" class="admit-buttons" >Modify</button>
        <button type="submit" formaction="view_report.php" name="view" class="admit-buttons" >View Report</button>
        <button type="submit" formaction="change_unit.php"name="change_unit" class="admit-buttons">Change Unit</button>
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
</script>
</div>
</div>
</div>
</div>

</body>
</html>
