<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_application";

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
    $mother_name = $_POST['mother_name'];
    $father_name = $_POST['father_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $category = $_POST['category'];
    $bloodgroup = $_POST['bloodgroup'];
    $shift = $_POST['shift'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $address = $_POST['address'];
    $profilePhoto = null;

    // Handle file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 2MB limit.');</script>";
            exit;
        }

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "<script>alert('Error: Invalid file type. Only JPEG, PNG, JPG are allowed.');</script>";
            exit;
        }

        // Define the upload directory
        $uploadDir = 'uploads/profile_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        // Define the path where the photo will be saved
        $filePath = $uploadDir . basename($fileName);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profilePhoto = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO applications 
            (Name, Register_no, Phone, Email, DoB, Age, Bloodgroup, Shift, Gender, Course, Address, Mother_name, Father_name, Category, ProfilePhoto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssisisssssss",
        $name,
        $register_no,
        $phone,
        $email,
        $dob,
        $age,
        $bloodgroup,
        $shift,
        $gender,
        $course,
        $address,
        $mother_name,
        $father_name,
        $category,
        $profilePhoto
    );

    // Execute query
    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    // Close statement
    $stmt->close();
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
      <form action="" method="post" class="nss-form" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"  />

        <label for="register_no">Register Number:</label>
        <input type="text" id="register_no" name="register_no"  />

        <label for="mother_name">Mother's Name:</label>
        <input type="text" id="mother_name" name="mother_name"  />

        <label for="name">Father's Name:</label>
        <input type="text" id="father_name" name="father_name"  />

        <label for="phone">Phone Number:</label>
        <input type="number" id="phone" name="phone"  />

        <label for="email">Email ID:</label>
        <input type="email" id="email" name="email"  />

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob"  />

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" >
          <option value="" disabled selected>Select </option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" ></input>

        <label for="category">Select Category:</label>
        <select id="category" name="category" >
          <option value="" disabled selected>Select </option>
          <option value="General">General</option>
          <option value="OBC">OBC</option>
          <option value="SC"> SC</option>
          <option value="ST"> ST</option>
        </select>

        <label for="shift">Select Shift:</label>
        <select id="shift" name="shift" >
          <option value="" disabled selected>Select </option>
          <option value="1">Shift 1</option>
          <option value="2">Shift 2</option>
          <option value="3">Shift 3</option>
        </select>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age"  />

        <label for="bloodgroup">Select Blood group:</label>
        <select id="bloodgroup" name="bloodgroup" >
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

        
        

        <label for="course">Course Name:</label>
        <input id="course" name="course" rows="3" ></input>

        

        <label for="profile_photo">Profile Photo (JPEG, PNG, JPG, max size: 2MB):</label>
    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/jpg"  />


        <div class="form-buttons">
          <button type="submit">Submit</button>
          <button type="reset">Reset</button>
        </div>
      </form>
    </div>
    <script src="script.js"></script>
    
  </body>
</html>
