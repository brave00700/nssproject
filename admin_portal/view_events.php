<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}            ?>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$results = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['event_unit']) && !empty($_POST['event_unit'])) {
    $event_unit = $_POST['event_unit'];

    // Fetch data based on selected unit
    $stmt = $conn->prepare("SELECT event_id, event_name, event_date, event_time, event_duration, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue, budget_pdf_path,event_unit
                            FROM events 
                            WHERE event_unit = ? OR event_unit = 10");
    $stmt->bind_param("s", $event_unit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no specific unit is selected, fetch all events
    $stmt = $conn->prepare("SELECT event_id, event_name, event_date, event_time, event_duration, poster_path, event_type, event_desc, teacher_incharge, student_incharge, event_venue, budget_pdf_path,event_unit FROM events");
    $stmt->execute();
    $result = $stmt->get_result();
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
</style>
   


</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
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
            <li><a href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a  href="manage_announcements.php"> Announcements</a></li>
            <li><a class="active" href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>
<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            
            <li><a class="active" href="view_events.php">View Events</a></li>
            <li><a  href="view_grievances.php">View Grievances</a></li>
            <li><a  href="manage_profile_requests.php">Profile Requests</a></li>
            <li><a href="manage_images.php">Upload Images to gallery</a></li>
            
            
            </ul>
        </div>
        <div class="widget">
        <div class="search_form">
        <h1>View Events</h1>
        <form method="post">
        <label for="event_unit">Select Unit:</label>
        <select name="event_unit" id="event_unit" required>
        <option value="" disabled selected>Choose Unit</option>
        <option value="1">Unit 1</option>
        <option value="2">Unit 2</option>
        <option value="3">Unit 3</option>
        <option value="4">Unit 4</option>
        <option value="5">Unit 5</option>
        <option value="ALL">ALL</option>
    </select>
    <button type="submit">View</button>
</form> </div>
<form  method="POST" onsubmit="return validateSelection()">
<div class="table-container">
<?php if (!empty($results)): ?>
            <table>
                <thead>
                    <tr>
                        
                    <th>Select</th>  
                    <th>Event ID</th>    
                        <th>Event Name</th>
                        <th>Event Unit</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration (hrs)</th>
                        <th>Poster</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Teacher In-Charge</th>
                        <th>Student In-Charge</th>
                        <th>Venue</th>
                        <th>Budget</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                        <td><input type="checkbox" name="event_id" value="<?= htmlspecialchars($row['event_id']) ?>"></td>
                            <td><?= htmlspecialchars($row['event_id']) ?></td>
                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                            <td><?= htmlspecialchars($row['event_unit']) ?></td>
                            <td><?= htmlspecialchars($row['event_date']) ?></td>
                            <td><?= htmlspecialchars($row['event_time']) ?></td>
                            <td><?= htmlspecialchars($row['event_duration']) ?></td>
                            <td>
                                <?php if (!empty($row['poster_path'])): ?>
                                    <img src="<?= htmlspecialchars($row['poster_path']) ?>" alt="Poster" style="width: 50px; height: 50px; object-fit: cover; border-radius: 20%;">
                                    <a href="<?= htmlspecialchars($row['poster_path']) ?>" target="_blank">Download</a>
                                <?php else: ?>
                                    No Poster
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['event_type']) ?></td>
                            <td><?= htmlspecialchars($row['event_desc']) ?></td>
                            <td><?= htmlspecialchars($row['teacher_incharge']) ?></td>
                            <td><?= htmlspecialchars($row['student_incharge']) ?></td>
                            <td><?= htmlspecialchars($row['event_venue']) ?></td>
                            <td>
                                <?php if (!empty($row['budget_pdf_path'])): ?>
                                    <a href="<?= htmlspecialchars($row['budget_pdf_path']) ?>" target="_blank">View</a>
                                <?php else: ?>
                                    No Budget File
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <p>No events found for the selected unit.</p>
        <?php endif; ?>
</div><br>
                    <button type="submit" formaction="modify_events.php" name="modify" class="admit-buttons">Modify</button>
               
                <button type="button" class="admit-buttons" onclick="redirectToPage()">Create New Event</button>
                <button type="submit" name="delete" class="delete-button"  >Delete Selected Events</button>
                </form>
                <?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    if (!empty($_POST['event_id'])) {
        // Ensure $_POST['event_id'] is always an array
        $event_ids = (array) $_POST['event_id']; // Convert string to array if needed

        // Database connection
        $conn = new mysqli("localhost", "root", "", "nss_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the DELETE statement
        $placeholders = implode(',', array_fill(0, count($event_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id IN ($placeholders)");

        // Convert all event IDs to integers and bind them dynamically
        $event_ids = array_map('intval', $event_ids); 
        $stmt->bind_param(str_repeat('i', count($event_ids)), ...$event_ids);

        if ($stmt->execute()) {
            echo "<script>alert('Selected events deleted successfully.'); window.location.href='view_events.php';</script>";
        } else {
            echo "<script>alert('Error deleting events. Please try again.'); window.location.href='view_events.php';</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Please select at least one event to delete.');</script>";
    }
}

?>

<script>
    function redirectToPage() {
        window.location.href = "create_events.php";
    }
    function redirectToDelete() {
        window.location.href = "delete_events.php";
    }
</script> 
        </div>
</div>
</div>
<script>
        function validateSelection() {
            const checkboxes = document.querySelectorAll('input[name="event_id"]:checked');
            if (checkboxes.length > 1) {
                alert("Please select only one event .");
                return false; // Prevent form submission
            }
            if (checkboxes.length === 0) {
                alert("Please select at least one event .");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        

    </script>
<script src="script.js"></script>
</body>
</html>

