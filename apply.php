<?php
require_once "config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

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
    // Calculate age in PHP
    $dob_date = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dob_date)->y; // Calculate age from DOB

    $category = $_POST['category'];
    $bloodgroup = $_POST['bloodgroup'];
    $shift = $_POST['shift'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $address = $_POST['address'];
    $profilePhoto = null;

    // Check if register number already exists
    $check_sql = "SELECT register_no FROM applications WHERE register_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $register_no);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Error: Register number already exists!'); window.location.href='apply.php';</script>";
        exit;
    }
    $check_stmt->close();
      
    // Check if register number already exists
    $check_sql = "SELECT register_no FROM students WHERE register_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $register_no);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Error: Register number already exists!'); window.location.href='apply.php';</script>";
        exit;
    }
    $check_stmt->close();


    // Handle file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 5 *  1024; // 500kbB

        // Validate file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Error: File size exceeds 500kb limit.');</script>";
            exit;
        }

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "<script>alert('Error: Invalid file type. Only JPEG, PNG, JPG are allowed.');</script>";
            exit;
        }

        // Define the upload directory
        $uploadDir = '/assets/uploads/profile_photo/';
        if (!is_dir('.' . $uploadDir)) {
            mkdir('.' . $uploadDir, 0777, true); // Create directory if not exists
        }
        
        // Define the path where the photo will be saved
        $filePath = $uploadDir . basename($fileName);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, '.' . $filePath)) {
            $profilePhoto = $filePath;
        } else {
            echo "<script>alert('Error: Failed to upload the profile photo. Please try again.');</script>";
            exit;
        }
    }

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO applications 
            (Name, Register_no, Phone, Email, DoB, Age, Bloodgroup, Shift, Gender, Course, Address, Mother_name, Father_name, Category,  profile_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssisssssssss",
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
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
  </head>
  <body>
    <!-- Header -->
    <?php include "header.php" ?>

    <!-- Navbar -->
    <nav
      class="navbar navbar-expand-sm navbar-dark p-0"
      style="background-color: var(--primary_blue)"
    >
      <div class="container-fluid d-flex justify-content-between">
        <!-- Toggler for collapsing links -->
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarCollapse"
          aria-controls="navbarCollapse"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Section for all links -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <div class="navbar-nav d-flex mx-auto">
            <a class="nav-item nav-link" href="home.php"
              >HOME</a
            >
            <a class="nav-item nav-link" href="about.php">ABOUT</a>
            <a class="nav-item nav-link" href="contact.php">CONTACT</a>
            <a class="nav-item nav-link" href="gallery.php">GALLERY</a>
            <a class="nav-item nav-link" href="announcements.php">ANNOUNCEMENTS</a>
            <a class="nav-item nav-link active" aria-current="page" href="apply.php">APPLY</a>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LOGIN</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="student/login.php" target="_blank">Student Login</a>
                <a class="dropdown-item" href="exe_portal/exe_login.php" target="_blank">Executive Login</a>
                <a class="dropdown-item" href="po_portal/po_login.php" target="_blank">Program Officer Login</a>
                <a class="dropdown-item" href="admin_portal/admin_login.php" target="_blank">Admin Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content  -->
     <div class="container mt-4">
        <!-- <span class="display-6 fw-normal">Register For NSS Volunteer</span> -->
        <form action="" method="post" class="nss-form" enctype="multipart/form-data"  onsubmit="calculateAge();return validateForm();">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name:</label>
            <input type="text"  class="form-control" id="name" name="name" required />
            </div>
            <div class="mb-3">
            <label for="register_no" class="form-label">Register Number:</label>
            <input type="text"  class="form-control" id="register_no" name="register_no" required  />
            </div>
            <div class="mb-3">
            <label for="mother_name" class="form-label">Mother's Name:</label>
            <input type="text" class="form-control" id="mother_name" name="mother_name" required />
            </div>
            <div class="mb-3">
            <label for="name" class="form-label">Father's Name:</label>
            <input type="text" class="form-control" id="father_name" name="father_name" required placeholder="Angel">
            </div>
            <div class="mb-3">
            <label for="phone" class="form-label">Phone Number:</label>
            <input type="number" class="form-control" id="phone" name="phone" required placeholder="987xxxxx13">
            </div>
            <div class="mb-3">
            <label for="email" class="form-label">Email ID:</label>
            <input type="email" class="form-control" id="email" name="email" required  placeholder="email@example.com">
            </div>
            <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" onchange="calculateAge()" required placeholder="01-01-2000">
            </div>
            <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <input type="text" class="form-control" id="address" name="address" required rows="3" placeholder="#25, Langford Road, Bengaluru - 27"></textarea>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender"  name="gender" aria-label="Default select example">
                <option selected disabled>Select Gender</option>
                <option value="MALE">Male</option>
                <option value="FEMALE">Female</option>
                <option value="OTHER">Other</option>
            </select>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category"  name="category" aria-label="Default select example">
                <option selected disabled>Select Category</option>
                <option value="general">General</option>
                <option value="obc">OBC</option>
                <option value="sc">SC</option>
                <option value="st">ST</option>
            </select>
            </div>
            <div class="mb-3">
                <label for="shift" class="form-label">Shift</label>
            <select class="form-select" id="shift" name="shift"  aria-label="Default select example">
                <option selected disabled>Select Shift</option>
                <option value="1">Shift-1</option>
                <option value="2">Shift-2</option>
                <option value="3">Shift-3</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="age" class="form-label">Age:</label>
            <input type="number" class="form-control" id="age" name="age" required readonly  placeholder="xx">
            </div>
            <div class="mb-3">
                <label for="bloodGroup" class="form-label">Bloodgroup</label>
            <select class="form-select" id="bloodGroup" name="bloodgroup" aria-label="Default select example">
                <option selected disabled>Select Bloodgroup</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">Course Name</label>
                <input type="text" class="form-control" name="course" id="course" placeholder="BCA">
            </div>
            <div class="mb-3">
                <label for="profile_photo" class="form-label">Profile Photo (JPEG, PNG, JPG, max size: 500kb):</label>
                <input type="file" class="form-control"  id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/jpg" required  />
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Register</button>
                <button type="reset" class="btn btn-primary">Reset</button>
              </div>
        </form>
     </div>

    <!-- Footer and logos -->
    <?php include "footer.php" ?>
    <script>
        function validateForm() {
            const nameRegex = /^[A-Za-z\s]+$/;
            const phoneRegex = /^[6-9]\d{9}$/;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            let name = document.getElementById("name").value;
            let father_name = document.getElementById("father_name").value;
            let mother_name = document.getElementById("mother_name").value;
            let address = document.getElementById("address").value;
            let phone = document.getElementById("phone").value;
            let email = document.getElementById("email").value;
            let dob = document.getElementById("dob").value;
            let course = document.getElementById("course").value;
            let age = document.getElementById("age").value;

             // Validate Name
            if (!nameRegex.test(name)) {
                alert("Name should contain only letters and spaces.");
                return false;
            }
           
            if (!nameRegex.test(father_name)) {
                alert("Father Name should contain only letters and spaces.");
                return false;
            }

            if (!nameRegex.test(mother_name)) {
                alert("Mother Name should contain only letters and spaces.");
                return false;
            }

            // Validate Phone Number
            if (!phoneRegex.test(phone)) {
                alert("Phone number must be exactly 10 digits.");
                return false;
            }

            // Validate Email
            if (!emailRegex.test(email)) {
                alert("Enter a valid email address.");
                return false;
            }
            
            // Validate Course Name
            if (!nameRegex.test(course)) {
                alert("Course name should contain only letters and spaces.");
                return false;
            }

            // Validate Age
            if (age < 17 || age > 35) {
                alert("Enter proper DoB details.");
                return false;
            }

            return true;
        }

        function calculateAge() {
            let dob = document.getElementById("dob").value;
            let dobDate = new Date(dob);
            let today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            let monthDiff = today.getMonth() - dobDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                age--;
            }

            document.getElementById("age").value = age;
        }
    </script>
  <script src="script.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>