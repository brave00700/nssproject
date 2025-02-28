<?php
session_start();

// Check if PO is logged in
if (!isset($_SESSION['po_id'])) {
    header("Location: ../login.html");
    exit();
}

$po_id = $_SESSION['po_id'];

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

$leave_requests = [];

// Fetch leave applications based on po_id
$stmt = $conn->prepare("SELECT approval_id, e_id, unit, department, from_date, to_date, no_of_days, reason, hod_dean_name, status FROM po_leave_approval WHERE e_id = ?");
$stmt->bind_param("s", $po_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $leave_requests[] = $row;
}

$stmt->close();
$conn->close();
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
        .table-container {
    width: 100%;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 500px; /* Adjust as needed */
    border: 1px solid #ccc;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Ensures horizontal scroll for smaller screens */
}

table th, table td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
    white-space: nowrap; /* Prevents text from wrapping */
}

table th {
    background-color: #007bff;
    color: white;
}

/* Styling for scrollbars */
.table-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
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
            <li><a   href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a class="active" href="po_view_events.php"> More</a></li>
            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
            <li><a  href="po_view_events.php"> View Events</a></li>
            <li><a class="active" href="po_view_leave_application.php"> View Leave Application</a></li>
            <li><a   href="po_view_grievance.php">View Grievance</a></li>

            </ul>
        </div>
        <div class="widget">
    <h1>Applied Leave Applications</h1>
    <form  method="POST" onsubmit="return validateSelection()">
    <div class="table-container">
        <?php if (!empty($leave_requests)): ?>
            <table>
                <thead>
                    <tr>
                    <th>Select</th>
                        <th>Approval ID</th>
                        <th>Employee ID</th>
                        <th>Unit</th>
                        <th>Department</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>No. of Days</th>
                        <th>Reason</th>
                        <th>HOD/Dean Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leave_requests as $row): ?>
                        <tr>
                        <td><input type="checkbox" name="approval_id" value="<?= htmlspecialchars($row['approval_id']) ?>"></td>
                            <td><?= htmlspecialchars($row['approval_id']) ?></td>
                            <td><?= htmlspecialchars($row['e_id']) ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td><?= htmlspecialchars($row['department']) ?></td>
                            <td><?= htmlspecialchars($row['from_date']) ?></td>
                            <td><?= htmlspecialchars($row['to_date']) ?></td>
                            <td><?= htmlspecialchars($row['no_of_days']) ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td><?= htmlspecialchars($row['hod_dean_name']) ?></td>
                            <td class="status-<?= ($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No leave applications found.</p>
        <?php endif; ?>
    </div><br>
    <button type="button" class="admit-buttons" onclick="redirectToPage()">Apply For Leave</button>
    <button type="submit" name="delete" class="delete-button"  >Delete Selected Leave Application</button>
    </form>
<?php
 if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    if (!empty($_POST['approval_id'])) {
        // Ensure $_POST['approval_id'] is always an array
        $approval_ids = (array) $_POST['approval_id']; // Convert string to array if needed

        // Database connection
        $conn = new mysqli("localhost", "root", "", "nss_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the DELETE statement
        $placeholders = implode(',', array_fill(0, count($approval_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM po_leave_approval WHERE approval_id IN ($placeholders)");

        // Convert all event IDs to integers and bind them dynamically
        $approval_ids = array_map('intval', $approval_ids); 
        $stmt->bind_param(str_repeat('i', count($approval_ids)), ...$approval_ids);

        if ($stmt->execute()) {
            echo "<script>alert('Selected Applications deleted successfully.'); window.location.href='po_view_leave_application.php';</script>";
        } else {
            echo "<script>alert('Error deleting Applications. Please try again.'); window.location.href='po_view_leave_application.php';</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Please select at least one application to delete.');</script>";
    }
}

?>
    </div>
    </div>
    <script>
    function redirectToPage() {
        window.location.href = "po_create_leave_application.php";
    }
   
</script> 
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
