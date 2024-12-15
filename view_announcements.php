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

// Fetch PDFs
$sql = "SELECT id, name FROM pdf_files";
$result = $conn->query($sql);

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
    <link rel="stylesheet" href="style2.css">
   <style>
    .view {
    background-color: #f1f1f1; /* Light grey background for contrast */
    border: 1px solid #ccc; /* Subtle border for separation */
    border-radius: 8px; /* Rounded corners */
    padding: 20px; /* Spacing inside the box */
    max-width: 600px; /* Restrict width for neat layout */
    margin: 20px auto; /* Center align with margin */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

.view h2 {
    font-size: 1.8rem; /* Slightly larger font for emphasis */
    font-weight: bold; /* Make the title bold */
    color: #333; /* Dark grey for good readability */
    margin-bottom: 20px; /* Spacing below the title */
    text-align: center; /* Center-align the title */
    text-transform: uppercase; /* Make the title all caps */
    border-bottom: 2px solid #007bff; /* Add a blue underline for styling */
    padding-bottom: 5px; /* Space between text and underline */
}

.view ul {
    list-style-type: none; /* Remove default bullet points */
    padding: 0; /* Remove default padding */
    margin: 0; /* Remove default margin */
}

.view ul li {
    margin: 10px 0; /* Add spacing between list items */
    font-size: 1rem; /* Normal font size for items */
    font-weight: 500; /* Slightly bold text for clarity */
}

.view ul li a {
    text-decoration: none; /* Remove underline from links */
    color: #007bff; /* Blue color for links */
    transition: color 0.3s ease; /* Smooth color transition on hover */
}

.view ul li a:hover {
    color: #0056b3; /* Darker blue on hover */
    text-decoration: underline; /* Add underline on hover for clarity */
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
            <li><a class="active" href="view_announcements.php">View Announcements</a></li>
            <li><a href="delete_announcements.php">Delete Announcements</a></li>
            
          </ul>
        </div>
        <div class="widget"> <div class="announcements">
            <h2>Announcements</h2>
            <div class="box"> <ul>
            <?php while ($row = $result->fetch_assoc()) { ?>
               <li>
                    <a href="download.php?id=<?php echo $row['id']; ?>" target="_blank"><?php echo $row['id'] ," : ",$row['name']; ?></a>
                    
                </li>
                
            <?php } ?>
        </ul></div>
        </div>
    
        </div>
    </div>
</div>
</body>
</html>
