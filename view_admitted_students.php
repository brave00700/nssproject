<?php
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

$results = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['unit'])) {
    $unit = $_POST['unit'];

    // Fetch data based on selected unit
    $stmt = $conn->prepare("SELECT Register_no, Name, Father_name, Mother_name, Phone, Email, Age, Gender, Address, Category, Bloodgroup, Shift, Course, ProfilePhoto 
                            FROM admitted_students 
                            WHERE Unit = ?");
    $stmt->bind_param("i", $unit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
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
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href=".css">
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

</style>
   


</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
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
        <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active"  href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a  href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="manage_inventory.php">Inventory</a></li>
        </ul>
    </div>
<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                
                <li><a  href="search_student.php">Search a Student</a></li>
                <li><a class="active" href="view_admitted_students.php">View Admitted Students<br> (Unit-wise)</a></li>
                <li><a href="modify_students_details.php">Modify Students Details</a></li>
                <li><a href="change_student_password.php">Change Student Password</a></li>
                
            </ul>
        </div>
        <div class="widget">
        <div class="search_form">
        <h1>View Admitted Students</h1>
        <form method="post">
        <label for="unit">Select Unit:</label>
        <select name="unit" id="unit" required>
        <option value="" disabled selected>Choose Unit</option>
        <option value="1">Unit 1</option>
        <option value="2">Unit 2</option>
        <option value="3">Unit 3</option>
        <option value="4">Unit 4</option>
        <option value="5">Unit 5</option>
    </select>
    <button type="submit">View</button>
</form> </div>
<div class="table-container">
<?php if (!empty($results)): ?>
    <table>
        <thead>
            <tr>
                <th>Profile Photo</th>
                <th>Register No</th>
                <th>Name</th>
                <th>Father's Name</th>
                <th>Mother's Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Category</th>
                <th>Blood Group</th>
                <th>Shift</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['ProfilePhoto'])): ?>
                            <img src="<?= htmlspecialchars($row['ProfilePhoto']) ?>" alt="Profile Photo" style='width: 50px; height: 50px; object-fit: cover; border-radius: 20%;'>
                        <?php else: ?>
                            No Photo
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['Register_no']) ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Father_name']) ?></td>
                    <td><?= htmlspecialchars($row['Mother_name']) ?></td>
                    <td><?= htmlspecialchars($row['Phone']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Age']) ?></td>
                    <td><?= htmlspecialchars($row['Gender']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td><?= htmlspecialchars($row['Category']) ?></td>
                    <td><?= htmlspecialchars($row['Bloodgroup']) ?></td>
                    <td><?= htmlspecialchars($row['Shift']) ?></td>
                    <td><?= htmlspecialchars($row['Course']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
    <p>No students found for the selected unit.</p>
<?php endif; ?>
</div>
        </div>
</div>
</div>
</body>
</html>

