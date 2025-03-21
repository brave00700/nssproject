<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
<header>
    <div class="header-container">
        <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
        <div class="header-content">
            <div class="header-text">NATIONAL SERVICE SCHEME</div>
            <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
            <div class="header-subtext">PROGRAM OFFICER PORTAL</div>
        </div>
        <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
    </div>
</header>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    <ul>
    <li><a  class="active" href="po_profile.php">Profile</a></li>
            <li><a  href="po_manage_application.php">Manage Applications</a></li>
            <li><a  href="po_view_admitted_students.php"> Manage Students</a></li>
            <li><a  href="po_manage_reports.php">Reports & Registers</a></li>
            
            <li><a  href="po_view_events.php"> More</a></li>

            <li><a href="po_logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a href="po_profile.php">View Profile</a></li>
                <li><a class="active" href="po_pass_change.php">Change Password</a></li>
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
            session_start();

            // Check session and timeout
            if (!isset($_SESSION['po_id'])) {
                header("Location: ../login.html");
                exit();
            }

            $po_id = $_SESSION['po_id'];
            require_once __DIR__ . '/../config_db.php';
            loadEnv(__DIR__ . '/../.env');

            $DB_HOST = getenv("DB_HOST");
            $DB_USER = getenv("DB_USER");
            $DB_PASS = getenv("DB_PASS");
            $DB_NAME = getenv("DB_NAME");

            if (isset($_POST['change'])) {
                $old_pass = $_POST['old_pass'];
                $new_pass = $_POST['new_pass'];
                $confirm_pass = $_POST['confirm_pass'];

                if ($new_pass != $confirm_pass) {
                    echo '<p style="color:red; text-align:center;">New passwords do not match</p>';
                } else {
                    // Connect to the database
                    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Verify old password
                    $stmt = $conn->prepare("SELECT password FROM staff WHERE user_id = ?");
                    $stmt->bind_param("s", $po_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        if ($row['password'] != $old_pass) {
                            echo '<p style="color:red; text-align:center;">Incorrect Old Password</p>';
                        } else {
                            $stmt2 = $conn->prepare("UPDATE staff SET password = ? WHERE user_id = ?");
                            $stmt2->bind_param("ss", $new_pass, $po_id);
                            if ($stmt2->execute()) {
                                echo '<p style="color:green; text-align:center;">Password Updated Successfully</p>';
                            } else {
                                echo '<p style="color:red; text-align:center;">Error updating password</p>';
                            }
                        }
                    } else {
                        echo '<p style="color:red; text-align:center;">User Not Found</p>';
                        header("Location: ../login.html");
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
