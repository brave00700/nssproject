<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1>  <b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Executive Portal</b><br>
        </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    
    <ul>
            <li><a  class="active" href="exe_profile.php">Profile</a></li>
            <li><a  href=".php">###</a></li>
            <li><a  href=".php"> ### </a></li>
            <li><a href=".php">###</a></li>
            
            <li><a href=".php"> ####</a></li>
            <li><a href="exe_logout.php">Logout</a></li>
        </ul>
    
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a class="active" href="exec_profile.php">View Profile</a></li>
                <li><a href="exe_pass_change.php">Change Password</a></li>
                
            </ul>
        </div>
        <div class="widget">
            <?php
            // Start session
            session_start();

            // Checking session timeout
            if (isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']) {
                session_unset();
                session_destroy();
                header("Location: exec_login.php");
                exit();
            }
            $_SESSION['last_seen'] = time();

            // Check if executive is logged in
            if (!isset($_SESSION['exec_id'])) {
                header("Location: exec_login.php");
                exit();
            }

            $exec_id = $_SESSION['exec_id'];

            // Create a connection
            $conn = new mysqli("localhost", "root", "", "staff_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT Name, Register_no, Phone, Email, DoB, Gender, Address, role, ProfilePhoto, Unit FROM staff_details WHERE User_id = ?");
            $stmt->bind_param("s", $exec_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photoPath = $row['ProfilePhoto'];

                echo "<table>
                        <tr>
                            <td>Profile</td>
                            <td><img src=\"../$photoPath\" style=\"width: 50px; height: 50px;\"></td>
                        </tr>
                        <tr>
                            <td>Register No</td>
                            <td>{$row['Register_no']}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{$row['Name']}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>{$row['role']}</td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                            <td>{$row['Unit']}</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>{$row['DoB']}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{$row['Gender']}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{$row['Phone']}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{$row['Email']}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{$row['Address']}</td>
                        </tr>
                    </table>";
            } else {
                echo "<p style='color:red; text-align:center;'>User Not Found</p>";
                header("Location: exec_login.php");
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
</body>
</html>
