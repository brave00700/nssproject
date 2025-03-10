<?php
session_start();

// Storing session variable
if(!$_SESSION['admin_id']){
    header("Location: ../login.html");
}            ?>
<?php
require_once __DIR__ . '/../config.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "nss_db";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register_no = $_POST['register_no'];

    // Fetch student email from the database
    $stmt = $conn->prepare("SELECT email FROM students WHERE Register_no = ?");
    $stmt->bind_param("s", $register_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Generate a secure password
        $password = generatePassword();

        // Update password in the database
        $stmt = $conn->prepare("UPDATE students SET password = ? WHERE register_no = ?");
        $stmt->bind_param("ss", $password, $register_no);
        if ($stmt->execute()) {
            // Send the generated password via email
            if (sendEmail($email, $password)) {
                echo "<script>alert('Password generated and sent to the student's email.');</script>";
                
            } else {
                
                echo "<script>alert('Failed to send email.');</script>";
            }
        } else {
            
            echo "<script>alert('Failed to update password in the database.');</script>";
        }
    } else {
        
        echo "<script>alert('Register number not found.');</script>";
    }

    $stmt->close();
}

// Close database connection
$conn->close();

/**
 * Function to generate a random password
 */
function generatePassword($length = 8) {
    $uppercase = chr(rand(65, 90));
    $lowercase = chr(rand(97, 122));
    $number = chr(rand(48, 57));
    $special = chr(rand(33, 47));
    $remainingLength = $length - 4;

    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $randomChars = substr(str_shuffle($characters), 0, $remainingLength);

    return str_shuffle($uppercase . $lowercase . $number . $special . $randomChars);
}

/**
 * Function to send email using PHPMailer
 */
function sendEmail($to, $password) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'testreset1882@gmail.com'; // Replace with your email
        $mail->Password = 'lgoykdxxwrdplacx'; // Replace with your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('testreset1882@gmail.com', 'NSS Admin');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Your New Password';
        $mail->Body = "Dear Student,<br><br>Your new password is: <b>$password</b><br><br>Regards,<br>NSS Admin";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="../adminportal.css">
   
</head>
<body>
<div class="header-container">
    <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
    <div class="header-content">
      <div class="header-text">NATIONAL SERVICE SCHEME</div>
      <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
      <div class="header-subtext">ADMIN PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a  href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
                        <li><a href="manage_more.php"> More</a></li>

            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
           
            
            <li><a href="manage_students.php">View Admitted Students</a></li>
            <li><a  href="view_credit_application.php">View Credits Application</a></li>
            <li><a class="active" href="change_student_password.php">Change Student Password</a></li>
            
            
          </ul>
        </div>
        <div class="widget">
            <div id="change_password">
            <h2>Generate New Password </h2>
    <form method="POST">
        <label for="register_no">Register Number:</label>
        <input type="text" id="register_no" name="register_no" required>
        <button type="submit">Generate Password</button>
    </form></div>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
