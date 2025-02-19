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

// Fetch PDFs
$sql = "SELECT id, name FROM announcements";
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
    <link rel="stylesheet" href="adminportal.css">
    <style></style>
</head>
<body>
    <div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">ಸೇಂಟ್ ಜೋಸೆಫ್ ವಿಶ್ವವಿದ್ಯಾಲಯ</b><br>
            <div style="font-size: 0.9rem;color: black;">#36 Lalbagh Road, Bengaluru 560027, Karnataka, India <br>
        </h1> 
        <img class="nsslogo" src="nss_logo.png" alt="logo" />
      </div>
      <marquee behavior="" direction="">Public - Private- Partnership University under RUSA 2.0 of MHRD (GOI) and Karnataka Act No. 24 of 2021</marquee>

    <div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="apply.php">Apply</a></li>
            <li><a href="login.html">Login</a></li>
            <li><a class="active" href="website_announcements.php">Announcements</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
    </div>
    <div class="main">
        <div class="announcements">
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
    <script src="script.js"></script>
</body>
</html>
