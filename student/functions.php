<?php
require_once __DIR__ . '/../config.php'; // For PHPMailer
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");
$EMAIL_USER = getenv("EMAIL_USER");
$EMAIL_PASS = getenv("EMAIL_PASS");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Commonly used functions
function checkSession(){
    // Starting a session
    session_start();

    // Checking user session timeout
    if(isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']){
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
    // Update last activity time
    $_SESSION['last_seen'] = time();

    // Storing session variable
    if(!isset($_SESSION['reg'])){
        header("Location: login.php");
        exit();
    }
    return $_SESSION['reg'];
}

function getDatabaseConnection(){
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    // Create a connection object
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function loginStudent($userid, $password){
    // Create a connection object for database
    $conn = getDatabaseConnection();
        
    $stmtGet = $conn->prepare("SELECT user_id, password, unit, login_attempts FROM students WHERE user_id = ?");
    $stmtGet->bind_param("s", $userid);
    $stmtGet->execute();
    $result = $stmtGet->get_result();

    if($result->num_rows > 0) {
        $cred = $result->fetch_assoc();
        $login_attempts = intval($cred['login_attempts']);
        if(password_verify($password, $cred['password']) && $login_attempts < 5){
            // Reset login counter
            $stmtUpdate = $conn->prepare("UPDATE students SET login_attempts = 0 WHERE user_id = ?");
            $stmtUpdate->bind_param("s", $userid);
            if($stmtUpdate->execute()){
                $_SESSION['reg'] = $userid;
                $_SESSION['last_seen'] = time();
                $_SESSION['timeout'] = 300;
                $_SESSION['unit'] = $cred['unit'];
                $message = "Logged in";
            }
            
        }else{
            if($login_attempts < 5){
                $login_attempts++;
                $stmtUpdate = $conn->prepare("UPDATE students SET login_attempts = ? WHERE user_id = ?");
                $stmtUpdate->bind_param("is", $login_attempts, $userid);
                if($stmtUpdate->execute()){
                    if($login_attempts === 5){
                        $message = 'Your account is locked!!! Please reset your password to continue using it';
                    }else{
                        $message = 'Invalid User ID or Password';
                    }
                }
                $stmtUpdate->close();
            }else{
                $message = 'Your account is locked!!! Please reset your password to continue using it';
            }
        }
    }
    else {
        $message = 'Invalid User ID or Password';
    }

    
    $stmtGet->close();
    $conn->close();

    return $message;
}
function getStudentData($conn, $user_id){
    $stmt = $conn->prepare("SELECT register_no, name, father_name, mother_name, phone, email, dob, gender, category, bloodgroup, shift, course, profile_photo, unit, address FROM students WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Return student details as an associative array
    }
    return null; // Return null if no student found 
}
//==========================================================
//forgot_pass.php
//==========================================================
function forgotPassRequest($user_id){
    // Create a connection object
    $conn = getDatabaseConnection();
                    
    $stmt = $conn->prepare("SELECT email FROM students WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $row_ad = $result->fetch_assoc();
        $email = $row_ad["email"];

        $stmt1 = $conn->prepare("SELECT * FROM password_resets WHERE user_id = ?");
        $stmt1->bind_param("s", $user_id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        //Generate token
        $new_token = generateToken();

        // If there is an entry
        if($result1->num_rows > 0){
            
            $stmt2 = $conn->prepare("UPDATE password_resets SET token = ?, expires_at = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE user_id = ?");
            $stmt2->bind_param("ss", $new_token, $user_id);
            if($stmt2->execute()){
                sendEmail($email, $new_token);
                return "Password Reset Successfull. Check your mail";
            }else{
                return "Error" . $conn->error;
            }
        }
        //Create a new entry
        else{
            $stmt2 = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES(?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
            $stmt2->bind_param("ss", $user_id, $new_token);
            if($stmt2->execute()){
                sendEmail($email, $new_token);
                return "Password Reset Successfull. Check your mail";
            }else{
                return "Error" . $conn->error;
            }
        }
    }
    else {
        return "Invalid User ID";
    }

    $stmt->close();
    $conn->close();
}
function sendEmail($to, $token) {
    global $EMAIL_USER, $EMAIL_PASS;
    $reset_url = "http://" . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : 'localhost') . "/student/forgot_pass_change.php?token=$token";
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $EMAIL_USER; // Replace with your email
        $mail->Password = $EMAIL_PASS; // Replace with your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email settings
        $mail->setFrom($EMAIL_USER, 'Admin Portal');
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
//==========================================================
//forgot_pass_change.php
//==========================================================
function isValidToken($conn, $token, &$user_id){
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $user_id = $row["user_id"];
        $expires_at = new DateTime($row["expires_at"]);
        $current = new DateTime();

        if($current < $expires_at){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function resetPassword($conn, $pass1, $pass2, $user_id){
    $message = '';
    if (empty($_POST['pass1']) || empty($_POST['pass2'])){
        $message = 'Please enter both passwords';
    }
    else if($pass1 != $pass2){
        $message = "Passwords don't match";
    }
    else{
        $hashedPassword = password_hash($pass1, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE students SET password = ?, login_attempts = 0 WHERE user_id = ?");
        $stmt->bind_param("ss", $hashedPassword, $user_id);
        if($stmt->execute()){
            $message = "Password changed successfully";
            
            $stmt2 = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt2->bind_param("s", $user_id);
            if(!$stmt2->execute()){
                $message = "Error occurred" . $conn->connect_error;
            }

        }
        $stmt->close();
    }
    return $message;
}
?>