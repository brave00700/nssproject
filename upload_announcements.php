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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    $fileName = $_FILES['pdf_file']['name'];
    $fileTmpName = $_FILES['pdf_file']['tmp_name'];
    $fileType = $_FILES['pdf_file']['type'];

    if ($fileType == "application/pdf") {
        $fileData = file_get_contents($fileTmpName);

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO pdf_files (name, file) VALUES (?, ?)");
        $stmt->bind_param("sb", $fileName, $null);

        $stmt->send_long_data(1, $fileData);

        if ($stmt->execute()) {
            echo "<script>alert('Announcement pdf uploaded successfully!');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo " <script>alert('Please upload a PDF file.')</script>";
    }
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
    <style>
    .upload {
    background-color: #f9f9f9; /* Light grey background for contrast */
    border: 1px solid #ccc; /* Subtle border for separation */
    border-radius: 8px; /* Rounded corners */
    padding: 20px; /* Spacing inside the box */
    max-width: 400px; /* Restrict width for neat layout */
    margin: 20px auto; /* Center align with margin */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

.upload h2 {
    font-size: 1.5rem; /* Larger font for title */
    font-weight: bold; /* Make title bold */
    color: #333; /* Dark grey text for better readability */
    margin-bottom: 20px; /* Spacing below the title */
    text-align: center; /* Center the title */
}

.upload form {
    display: flex;
    flex-direction: column; /* Stack form elements vertically */
    gap: 15px; /* Add spacing between form elements */
}

.upload label {
    font-size: 1rem; /* Normal font size for the label */
    color: #555; /* Slightly lighter text color */
    font-weight: 500; /* Medium weight for better visibility */
}

.upload input[type="file"] {
    padding: 5px; /* Space around the text in the input */
    border: 1px solid #ccc; /* Light border around the input */
    border-radius: 4px; /* Rounded corners */
    background-color: #fff; /* White background */
    font-size: 1rem; /* Consistent font size */
}

.upload button {
    padding: 10px 15px; /* Space inside the button */
    font-size: 1rem; /* Normal font size */
    font-weight: bold; /* Bold text for the button */
    color: #fff; /* White text */
    background-color: #007bff; /* Blue background */
    border: none; /* Remove default border */
    border-radius: 4px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease; /* Smooth color transition */
}

.upload button:hover {
    background-color: #0056b3; /* Darker blue on hover */
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
            <li><a class="active" href="upload_announcements.php">Upload Announcements</a></li>
            <li><a href="view_announcements.php">View Announcements</a></li>
            <li><a href="delete_announcements.php">Delete Announcements</a></li>
            
          </ul>
        </div>
        <div class="widget">
        <div class="upload">
        <h2>Upload a PDF File</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Select PDF File:</label>
        <input type="file" name="pdf_file" required>
        <button type="submit">Upload</button>
    </form></div>
        </div>
    </div>
</div>
</body>
</html>
