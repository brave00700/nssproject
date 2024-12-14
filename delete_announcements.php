<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pdf_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pdf_id'])) {
    $pdfId = intval($_POST['pdf_id']);

    // Check if the PDF exists in the database
    $stmt = $conn->prepare("SELECT id, name FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $pdfId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the PDF from the database
        $deleteStmt = $conn->prepare("DELETE FROM pdf_files WHERE id = ?");
        $deleteStmt->bind_param("i", $pdfId);
        if ($deleteStmt->execute()) {
            echo "<script>alert('PDF file deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting the PDF file.');</script>";
        }
        $deleteStmt->close();
    } else {
        echo "<script>alert('No PDF found with the given ID.');</script>";
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
    <title>Delete Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <style>
    .delete {
        background-color: #fef8f8; /* Light pink background */
        border: 1px solid #f5c6cb; /* Red border for warning */
        border-radius: 8px; /* Rounded corners */
        padding: 20px; /* Spacing inside the box */
        max-width: 400px; /* Restrict width for neat layout */
        margin: 20px auto; /* Center align with margin */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
    }

    .delete h2 {
        font-size: 1.5rem; /* Larger font for title */
        font-weight: bold; /* Make title bold */
        color: #b71c1c; /* Dark red text for emphasis */
        margin-bottom: 20px; /* Spacing below the title */
        text-align: center; /* Center the title */
    }

    .delete form {
        display: flex;
        flex-direction: column; /* Stack form elements vertically */
        gap: 15px; /* Add spacing between form elements */
    }

    .delete label {
        font-size: 1rem; /* Normal font size for the label */
        color: #555; /* Slightly lighter text color */
        font-weight: 500; /* Medium weight for better visibility */
    }

    .delete input[type="number"] {
        padding: 5px; /* Space around the text in the input */
        border: 1px solid #ccc; /* Light border around the input */
        border-radius: 4px; /* Rounded corners */
        background-color: #fff; /* White background */
        font-size: 1rem; /* Consistent font size */
    }

    .delete button {
        padding: 10px 15px; /* Space inside the button */
        font-size: 1rem; /* Normal font size */
        font-weight: bold; /* Bold text for the button */
        color: #fff; /* White text */
        background-color: #dc3545; /* Red background */
        border: none; /* Remove default border */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor on hover */
        transition: background-color 0.3s ease; /* Smooth color transition */
    }

    .delete button:hover {
        background-color: #b71c1c; /* Darker red on hover */
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
            <li><a class="active" href="manage_upload.php">Manage Announcements</a></li>
            <li><a  href="">###</a></li>
            <li><a href="">####</a></li>
            <li><a href="">####</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav"> 
          <ul>
            <li><a  href="upload_announcements.php">Upload Announcements</a></li>
            <li><a href="view_announcements.php">View Announcements</a></li>
            <li><a class="active" href="delete_announcements.php">Delete Announcements</a></li>
            
          </ul>
        </div>
        <div class="widget">
        <div class="delete">
    <h2>Delete a Announcement</h2>
    <form method="POST">
        <label for="pdf_id">Enter PDF ID to Delete:</label>
        <input type="number" name="pdf_id" id="pdf_id" placeholder="Enter ID" required>
        <button type="submit">Delete</button>
    </form>
</div>
        </div>
    </div>
</div>
</body>

</html>
