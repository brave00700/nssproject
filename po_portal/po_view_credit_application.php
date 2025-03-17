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
if(!$_SESSION['po_id'] || !$_SESSION['unit']){
    header("Location: ../login.html");
}   



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in PO's unit
$po_unit = $_SESSION['unit'];

$results = [];

// Handle filtering and searching
$whereClauses = ["students.unit = ?"]; // Ensure only students from the PO's unit are shown
$params = [$po_unit];
$types = "s"; // Unit is stored as ENUM (string)

// Filter by status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['status']) && $_POST['status'] !== "") {
    $whereClauses[] = "credits.status = ?";
    $params[] = $_POST['status'];
    $types .= "s";
}

// Search by register number
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_no']) && !empty($_POST['register_no'])) {
    $whereClauses[] = "credits.register_no = ?";
    $params[] = $_POST['register_no'];
    $types .= "s";
}

// Build query dynamically with JOIN to get students' unit
$query = "SELECT credits.credit_id, credits.register_no, credits.credits, credits.status
          FROM credits
          INNER JOIN students ON credits.register_no = students.user_id";

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
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
    <title>View Credit Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../assets/css/style.css">
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

        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
            max-width: 100%;
            border: 1px solid #ccc;
            padding: 5px;
            background-color: #f9f9f9;
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

        .admit-buttons, .delete-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .admit-buttons {
            background-color: #007bff;
        }

        .admit-buttons:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
<header>
  <div class="header-container">
    <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
    <div class="header-content">
      <div class="header-text">NATIONAL SERVICE SCHEME</div>
      <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
      <div class="header-subtext">PROGRAM OFFICER PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a   href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a class="active" href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a href="po_approve_attendance.php">Attendance</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
        </ul>
    </div>


    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  href="po_view_admitted_students.php">View Admitted Students</a></li>
                <li><a class="active"  href="po_view_credit_application.php">View Credit Application</a></li>
                

            </ul>
        </div>
        <div class="widget">
<h1 style="text-align:center;">View Credit Applications</h1>
<div class="search_form_container">
<!-- Search by Status Form -->
<div class="search_form">
    <form method="post" >
        <label for="status">Filter by Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="APPROVED">Approved</option>
            <option value="PENDING">Pending</option>
            <option value="PO_APPROVED">PO Approved</option>
            <option value="REJECTED">Rejected</option>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<!-- Search by Register No Form -->
<div class="search_form">
    <form method="post">
        <label for="register_no">Search by Register No:</label>
        <input type="text" name="register_no" id="register_no" placeholder="Enter Register No">
        <button type="submit">Search</button>
    </form>
</div>
</div>
<!-- Display Credit Applications -->
<form method="POST" onsubmit="return validateSelection()">
    <div class="table-container">
        <?php if (!empty($results)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Select</th>  
                        <th>Credit ID</th>    
                        <th>Register No</th>
                        <th>Credits</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="credit_id" value="<?= htmlspecialchars($row['credit_id']) ?>"></td>
                            <td><?= htmlspecialchars($row['credit_id']) ?></td>
                            <td><?= htmlspecialchars($row['register_no']) ?></td>
                            <td><?= htmlspecialchars($row['credits']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <p>No applications found.</p>
        <?php endif; ?>
    </div>
    <br>
    <button type="submit" formaction="po_view_report_approve.php" name="modify"  class="admit-buttons" >View Report and Approve</button>
    <button type="submit" name="delete" class="delete-button">Delete Selected Application</button>
</form>

<script>
     function validateSelection() {
            const checkboxes = document.querySelectorAll('input[name="credit_id"]:checked');
            if (checkboxes.length > 1) {
                alert("Please select only one application .");
                return false; // Prevent form submission
            }
            if (checkboxes.length === 0) {
                alert("Please select at least one application.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
</script>
</div>
</div>
</div>
<script src="script.js"></script>
</body>
</html>

<?php
// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    if (!empty($_POST['credit_id'])) {
        $credit_ids = array_map('intval', $_POST['credit_id']);
        $placeholders = implode(',', array_fill(0, count($credit_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM credits WHERE credit_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($credit_ids)), ...$credit_ids);

        if ($stmt->execute()) {
            echo "<script>alert('Selected applications deleted successfully.'); window.location.href='po_view_credit_application.php';</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>
