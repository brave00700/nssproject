<?php
// Creating a new session
session_start();

// Error message to be displayed
$message = "";
  
// Checking for login
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['login'])){
    if (!empty($_POST['id']) && !empty($_POST['pass'])){
        $lreg = $_POST['id'];
        $lpass = $_POST['pass'];

        // Create a connection object for database
        $conn = new mysqli("localhost", "root", "", "nss_db");
        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT user_id, password, unit, login_attempts FROM students WHERE user_id = ?");
        $stmt->bind_param("s", $lreg);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $cred = $result->fetch_assoc();
            $login_attempts = intval($cred['login_attempts']);
            if($cred['password'] == $lpass && $login_attempts < 5){
                // Reset login counter
                $stmt1 = $conn->prepare("UPDATE students SET login_attempts = 0 WHERE user_id = ?");
                $stmt1->bind_param("s", $lreg);
                if($stmt1->execute()){
                    $_SESSION['reg'] = $lreg;
                    $_SESSION['last_seen'] = time();
                    $_SESSION['timeout'] = 300;
                    $_SESSION['unit'] = $cred['unit'];
                    header("Location: profile.php");
                    exit();
                }
                
            }else{
                if($login_attempts < 5){
                    $login_attempts++;
                    $stmt1 = $conn->prepare("UPDATE students SET login_attempts = ? WHERE user_id = ?");
                    $stmt1->bind_param("is", $login_attempts, $lreg);
                    if($stmt1->execute()){
                        $message = 'Invalid User ID or Password';
                    }
                }else{
                    $message = 'Your account is locked!!! Please reset your password to continue using it';
                }
            }
        }
        else {
            $message = 'Invalid User ID or Password';
        }

        $stmt->close();
        $conn->close();
    }else{
        $message = 'Please enter both User ID and Password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - NSS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        form {
        background-color: #ffffff; 
        padding: 1.5rem; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        width: 350px; 
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

    .main {
        display: flex;
        flex-direction: column;
        justify-content: center; 
        align-items: center; 
        background-color: #f7f7f7; 
    }

    p {
        width: 350px;
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        background-color: #ffb1005c; 
        color:rgb(255, 0, 0);
        font-weight: 700;
        padding: 1rem;   
        text-align: center;
    }
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
            <li><a  class="active" href="">Log In</a></li>
        </ul>
    </div>

    <div class="main">
            <form method="post">
                <table>
                    <tr>
                        <td class="label">User ID</td>
                        <td><input type="text" name='id' ></td>
                    </tr>
                    <tr>
                        <td class="label">Password</td>
                        <td><input type="password" name='pass' ></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button name="login" type="submit">Login</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><a href="forgot_pass.php">Forgot Password</a></td>
                    </tr>
                </table>
            </form>
            <?php if ($message): ?>
                <p><?php echo $message ?>
            <?php endif; ?>
</div>
</body>
</html>
