<?php 
    include "exe_header.php"
?>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a class="active" href="exec_profile.php">View Profile</a></li>
                <li><a href="exe_pass_change.php">Change Password</a></li>
                <li><a href="exe_logout.php">Logout</a></li>
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
            $conn = new mysqli("localhost", "root", "", "nss_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT name, user_id, phone, email, dob, gender, address, role, profile_photo, unit FROM staff WHERE user_id = ?");
            $stmt->bind_param("s", $exec_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $photoPath = $row['profile_photo'];

                echo "<table>
                        <tr>
                            <td>Profile</td>
                            <td><img src=\"./$photoPath\" style=\"width: 50px; height: 50px;\"></td>
                        </tr>
                        <tr>
                            <td>Register No</td>
                            <td>{$row['user_id']}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{$row['name']}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>{$row['role']}</td>
                        </tr>
                        <tr>
                            <td>Unit</td>
                            <td>{$row['unit']}</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>{$row['dob']}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{$row['gender']}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{$row['phone']}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{$row['email']}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{$row['address']}</td>
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
<script src="script.js"></script>
</body>
</html>
