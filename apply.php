<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "nss_application"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$name = $_POST['name'];
$register_no = $_POST['register_no'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$dob = $_POST['dob'];
$age = $_POST['age'];
$bloodgroup = $_POST['bloodgroup'];
$shift = $_POST['shift'];
$gender = $_POST['gender'];
$medical = $_POST['medical'];
$reason = $_POST['reason'];

// Prepare SQL statement
$sql = "INSERT INTO applications (Name, Register_no, Phone, Email, DoB, Age, Bloodgroup, Shift, Gender, Medical, Reason) 
        VALUES ('$name', '$register_no', '$phone', '$email', '$dob', '$age', '$bloodgroup', '$shift', '$gender', '$medical', '$reason')";



if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Application submitted successfully!');</script>";
} else {
    echo "<script>alert('Error: Unable to submit application. Please try again later.');</script>";
}
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NSS Apply</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
      integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="logo-container">
      <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
      <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
          <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
          <b style="font-size: 1.3rem">ಸೇಂಟ್ ಜೋಸೆಫ್ ವಿಶ್ವವಿದ್ಯಾಲಯ</b><br>
          <div style="font-size: 0.9rem;color: black;">#36 Lalbagh Road, Bengaluru 560027, Karnataka, India <br>
          </div> 
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
        <li><a class="active" href="apply.html">Apply</a></li>
        <li><a href="login.html">Login</a></li>
        <li><a href="website_announcements.php">Announcements</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>
    </div>
    <div class="mainapply">
      <h2>Application Form</h2>
      <form action="" method="post" class="nss-form">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="register_no">Register Number:</label>
        <input type="text" id="register_no" name="register_no" required />

        <label for="phone">Phone Number:</label>
        <input type="number" id="phone" name="phone" required />

        <label for="email">Email ID:</label>
        <input type="email" id="email" name="email" required />

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required />

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required />
        <label for="bloodgroup">Select Blood group:</label>
        <select id="bloodgroup" name="bloodgroup" required>
          <option value="" disabled selected>Select </option>
          <option value="A+">A+</option>
          <option value="A-">A-</option>
          <option value="B+">B+</option>
          <option value="B-">B-</option>
          <option value="AB+">AB+</option>
          <option value="AB-">AB-</option>
          <option value="O+">O+</option>
          <option value="O-">O-</option>
        </select>
        <label for="shift">Select Shift:</label>
        <select id="shift" name="shift" required>
          <option value="" disabled selected>Select </option>
          <option value="shift1">Shift 1</option>
          <option value="shift2">Shift 2</option>
          <option value="shift3">Shift 3</option>
        </select>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
          <option value="" disabled selected>Select </option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <label for="medical">Medical Condition (if any):</label>
        <textarea id="medical" name="medical" rows="3"></textarea>

        <label for="reason">Why do you want to join NSS?</label>
        <textarea id="reason" name="reason" rows="4" required></textarea>

        <div class="form-buttons">
          <button type="submit">Submit</button>
          <button type="reset">Reset</button>
        </div>
      </form>
    </div>
    <script src="script.js"></script>
    
  </body>
</html>
