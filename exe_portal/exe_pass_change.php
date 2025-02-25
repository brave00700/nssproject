<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        input, .label {
            font-size: 1.1rem;
        }
        .label {
            text-align: right;
        }
        input {
            width: 200px;
        }
        button {
            background-color: #FFA200;
            border: none;
            color: #FFFFFF;
            padding: 0.5rem;
            font-weight: 700;
            width: 200px;
        }
        button:active {
            background-color: #e69202;
        }
    </style>
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
                <li><a href="exe_profile.php">View Profile</a></li>
                <li><a class="active" href="exe_pass_change.php">Change Password</a></li>
                
            </ul>
        </div>
        <div class="widget">
            <form method="post">
                <table>
                    <tr>
                        <td class="label">Old Password</td>
                        <td><input type="password" name='old_pass' required></td>
                    </tr>
                    <tr>
                        <td class="label">New Password</td>
                        <td><input type="password" name='new_pass' required></td>
                    </tr>
                    <tr>
                        <td class="label">Confirm Password</td>
                        <td><input type="password" name='confirm_pass' required></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button name="change" type="submit">Change Password</button></td>
                    </tr>
                </table>
            </form>
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

            // Checking for change password request
            if (isset($_POST['change'])) {
                $old_pass = $_POST['old_pass'];
                $new_pass = $_POST['new_pass'];
                $confirm_pass = $_POST['confirm_pass'];

                if ($new_pass != $confirm_pass) {
                    echo '<p style="color:red; text-align:center;">New passwords do not match</p>';
                } else {
                    // Create a connection
                    $conn = new mysqli("localhost", "root", "", "staff_db");
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $stmt = $conn->prepare("SELECT Password FROM staff_details WHERE User_id = ?");
                    $stmt->bind_param("s", $exec_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        if ($row['Password'] != $old_pass) {
                            echo '<p style="color:red; text-align:center;">Incorrect Old Password</p>';
                        } else {
                            $stmt2 = $conn->prepare("UPDATE staff_details SET Password = ? WHERE User_id = ?");
                            $stmt2->bind_param("ss", $new_pass, $exec_id);
                            if ($stmt2->execute()) {
                                echo '<p style="color:green; text-align:center;">Password Updated Successfully</p>';
                            } else {
                                echo '<p style="color:red; text-align:center;">Error updating password</p>';
                            }
                        }
                    } else {
                        echo '<p style="color:red; text-align:center;">User Not Found</p>';
                        header("Location: exe_login.php");
                    }
                    $stmt->close();
                    $conn->close();
                }
            }
            ?>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
