<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();

// Create a connection object
$conn = getDatabaseConnection();

// Fetch student data
$result = getStudentData($conn, $reg);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .widget {
    width: 100%;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.profile-header {
    text-align: center;
    margin-bottom: 20px;
}

.profile-img {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #007bff;
}

.profile-header h2 {
    font-size: 22px;
    color: #333;
    margin: 10px 0 5px;
}

.profile-header p {
    font-size: 14px;
    color: #555;
}

/* Unified Profile Info */
.profile-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Flexible layout */
    gap: 12px;
    text-align: left;
    padding: 10px 0;
}

.profile-info div {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
    color: #333;
}
@media (max-width: 600px) {
    .widget {
        width: 95%;
        padding: 15px;
    }

    .profile-img {
        width: 70px;
        height: 70px;
    }

    .profile-info {
        grid-template-columns: 1fr; /* Stack in a single column on small screens */
    }
}

    </style>
</head>
<body>
<?php include "header.php" ?>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a  href="events.php">Events</a></li>
            <li><a  href="grievance.php">Grievience</a></li>
            <li><a  href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a class="active" href="profile.php">My Profile</a></li>
            <li><a href="update_profile.php">Request Profile Update</a></li>
            <li><a href="pass_change.php">Change Password</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="widget">
            <?php
            if ($result) {
                $photoPath = $result['profile_photo'];?>
                        <div class="profile-header">
<<<<<<< Updated upstream
                            <img src="..<?php echo $photoPath; ?>" alt="Profile Picture" class="profile-img">
                            <h2><?php echo $result['name']; ?></h2>
                            <p><strong>Register No:</strong> <?php echo $result['register_no']; ?></p>
=======
                            <img src="../<?php echo $photoPath; ?>" alt="Profile Picture" class="profile-img">
                            <h2><?php echo $row['name']; ?></h2>
                            <p><strong>Register No:</strong> <?php echo $row['register_no']; ?></p>
>>>>>>> Stashed changes
                        </div>
                        
                        <div class="profile-info">
                            <div><strong>Unit:</strong> <?php echo $result['unit']; ?></div>
                            <div><strong>Shift:</strong> <?php echo $result['shift']; ?></div>
                            <div><strong>Course:</strong> <?php echo $result['course']; ?></div>
                            <div><strong>Phone:</strong> <?php echo $result['phone']; ?></div>
                            <div><strong>Email:</strong> <?php echo $result['email']; ?></div>
                            <div><strong>Address:</strong> <?php echo $result['address']; ?></div>
                            <div><strong>Father's Name:</strong> <?php echo $result['father_name']; ?></div>
                            <div><strong>Mother's Name:</strong> <?php echo $result['mother_name']; ?></div>
                            <div><strong>Date of Birth:</strong> <?php echo $result['dob']; ?></div>
                            <div><strong>Gender:</strong> <?php echo $result['gender']; ?></div>
                            <div><strong>Category:</strong> <?php echo $result['category']; ?></div>
                            <div><strong>Blood Group:</strong> <?php echo $result['bloodgroup']; ?></div>
                        </div>


            <?php }else {
                echo "User Not Found";
                header("Location: login.php");
            }
            ?>
        </div>
    </div>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
