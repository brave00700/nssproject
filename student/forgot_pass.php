<?php
// Creating a new session
session_start();

require_once __DIR__ . '/../config.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $token) {
    $reset_url = "http://$_SERVER[HTTP_HOST]/NMS/student/forgot_pass_change.php?token=$token";
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
        $mail->setFrom('testreset1882@gmail.com', 'Admin Portal');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password';
        $mail->Body = "Dear Student,<br><br>You have successfully reset your password. Please click the below link to reset your password :<br> <b><a href=\"$reset_url\">Click here</a></b><br><br>Regards,<br>NSS Portal";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
function generateToken($length = 15) {
    $lowercase = chr(rand(97, 122));
    $number = chr(rand(48, 57));
    $remainingLength = $length - 2;

    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $randomChars = substr(str_shuffle($characters), 0, $remainingLength);

    return str_shuffle($lowercase . $number . $randomChars);
}

$current = new DateTime();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        form {
        background-color: #ffffff; 
        padding: 1.5rem; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        width: 400px; 
        margin: auto; 
    }

    table {
        width: 100%;
    }

    .label {
        font-size: 1rem;
        color: #333;
        text-align: right; 
        padding-right: 0.8rem; 
    }

    input {
        width: 100%; 
        padding: 0.6rem; 
        margin-bottom: 0.8rem; 
        border: 1px solid #ccc; 
        border-radius: 6px; 
        font-size: 1rem;
    }

    button {
        width: 100%; 
        padding: 0.6rem; 
        font-size: 1rem;
        font-weight: bold; 
        color: #fff; 
        background-color: #ffa200; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        transition: all 0.3s ease; 
    }

    button:hover {
        background-color: #e69202; 
    }

    button:active {
        background-color: #cc7d02; 
        transform: scale(0.98); 
    }

    /* .main {
        display: flex;
        flex-direction: column;
        justify-content: center; 
        align-items: center; 
        height: 60vh; 
        background-color: #f7f7f7; 
    } */
</style>
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="../assets/icons/sju_logo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Student Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../assets/icons/nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a  class="active" href="">Reset Password</a></li>
        </ul>
    </div>

    <div class="main">
            <form method="post">
                <table>
                    <tr>
                        <td class="label">User ID</td>
                        <td><input type="text" name='id' ></td>
                    </tr>
                        <td></td>
                        <td><button type="submit" name="reset">Forgot Password</button></td>
                    </tr>
                </table>
            </form>
            <?php
            

            // Checking for request
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])){
                if (!empty($_POST['id'])){
                    $lreg = $_POST['id'];

                    // Create a connection object
                    $conn = new mysqli("localhost", "root", "", "nss_db");
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("SELECT email FROM students WHERE user_id = ?");
                    $stmt->bind_param("s", $lreg);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if($result->num_rows > 0) {
                        $row_ad = $result->fetch_assoc();
                        $email = $row_ad["email"];

                        $stmt1 = $conn->prepare("SELECT * FROM password_resets WHERE user_id = ?");
                        $stmt1->bind_param("s", $lreg);
                        $stmt1->execute();
                        $result1 = $stmt1->get_result();

                        //Generate token
                        $new_token = generateToken();

                        // If there is an entry
                        if($result1->num_rows > 0){
                            
                            $stmt2 = $conn->prepare("UPDATE password_resets SET token = ?, expires_at = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE user_id = ?");
                            $stmt2->bind_param("ss", $new_token, $lreg);
                            if($stmt2->execute()){
                                echo "Password Update Successfull";
                                sendEmail($email, $new_token);
                            }else{
                                echo "Error" . $conn->error;
                            }
                        }
                        //Create a new entry
                        else{
                            $stmt2 = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES(?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
                            $stmt2->bind_param("ss", $lreg, $new_token);
                            if($stmt2->execute()){
                                echo "Password Reset Successfull";
                                sendEmail($email, $new_token);
                            }else{
                                echo "Error" . $conn->error;
                            }
                        }
                    }
                    else {
                        echo 'Invalid UserID';
                    }

                    $stmt->close();
                    $conn->close();
                }else{
                    echo 'Invalid UserID';
                }
            }
?>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
