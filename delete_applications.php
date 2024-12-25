<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Delete</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminportal.css">
    <style>
        
    </style>
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru.<br>
        <b style="font-size: 1.3rem">Admin Portal</b><br>
    </h1>
    <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a class="active" href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_announcements.php">Manage Announcements</a></li>
            <li><a   href="manage_passwords.php">Accounts & Passwords</a></li>
            <li><a href="">####</a></li>
            <li><a href="">####</a></li>
        </ul>
    </div>
    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="show_applications.php">Show All Applications</a></li>
            <li><a href="search_applications.php">Search Applications</a></li>
            <li><a class="active" href="delete_applications.php">Delete Applications</a></li>
            <li><a href="admit_student.php">Admit Students</a></li>
          </ul>
        </div>
        <div class="widget">
    <div class="delete-container">
        <form method="POST" action="">
            <input type="text" name="delete_register_no" placeholder="Enter Register Number to Delete" required>
            <button type="submit">Delete</button>
        </form>
    </div>
    <div class="message">
        <?php
        // Handle form submission for deleting a student
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_register_no']) && !empty($_POST['delete_register_no'])) {
            // Connect to the database
            $conn = new mysqli("localhost", "root", "", "nss_application");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get the register number from POST
            $delete_register_no = $conn->real_escape_string($_POST['delete_register_no']);

            // Check if the register number exists
            $check_sql = "SELECT * FROM applications WHERE register_no = '$delete_register_no'";
            $check_result = $conn->query($check_sql);

            if ($check_result->num_rows > 0) {
                // Delete the record
                $delete_sql = "DELETE FROM applications WHERE register_no = '$delete_register_no'";
                if ($conn->query($delete_sql) === TRUE) {
                    echo "Record with Register Number $delete_register_no has been deleted successfully.";
                } else {
                    echo "Error deleting record: " . $conn->error;
                }
            } else {
                echo "No record found with Register Number $delete_register_no.";
            }

            $conn->close();
        }
        ?>
    </div>
    </div>
    </div>
</div>
</body>
</html>
