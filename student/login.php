<?php
require_once __DIR__ . '/functions.php';

// Creating a new session
session_start();

// Notifications not required until logged in
$hideNotifications = true;

// Check if already logged in
if(isset($_SESSION['reg']) && isset($_SESSION['last_seen'])){
    if((time() - $_SESSION['last_seen']) < $_SESSION['timeout']){
        header("Location: profile.php");
        exit();
    }else {
        session_unset();
        session_destroy();
    }
}

// Error message to be displayed
$message = "";
  
// Checking for login
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['login'])){
    if (!empty($_POST['id']) && !empty($_POST['pass'])){
        $message = loginStudent($_POST['id'], $_POST['pass']);
        if($message === "Logged in"){
            header("Location: profile.php");
            exit();
        }
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
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        form {
        background-color: #ffffff; 
        padding: 1.5rem; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        max-width: 350px; 
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

    td button {
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

    td button:hover {
        background-color: #e69202; 
    }

    td button:active {
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

    p.msg {
        width: 350px;
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        background-color: #ffb1005c; 
        color:rgb(255, 0, 0);
        font-weight: 700;
        padding: 1rem;   
        text-align: center;
    }
    .forgot {
        font-size: 0.9rem;
    }
    .forgot:hover {
        color: #e69202 !important;
    }
    .nav > ul {
        display: block !important;
    }
    @media only screen and (max-width: 399px) {
        tr {
            display: flex;
            flex-direction: column;
        }
        tr .label {
            text-align: left;
        }
    }
</style>
</head>
<body>
    <?php include "header.php" ?>
   
<div class="nav">
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
                        <td colspan="2"><button name="login" type="submit">Login</button></td>
                    </tr>
                    <tr>
                        <th colspan="2"><a href="forgot_pass.php" class="forgot" style="text-decoration: none; color: #000;">Forgot Password ?</a></th>
                    </tr>
                </table>
            </form>
            <?php if ($message): ?>
                <p class="msg"><?php echo $message ?>
            <?php endif; ?>
</div>
<script>
    // Adjust screen height
    function adjustMainHeight() {
    const logoHeight = document.querySelector('header').offsetHeight;
    const navHeight = document.querySelector('.nav').offsetHeight;
    const mainElement = document.querySelector('.main');

    mainElement.style.minHeight = `calc(100vh - ${logoHeight + navHeight}px - 40px)`;
    }
     // Run on page load
     window.addEventListener('load', adjustMainHeight);
    // Run on window resize
    window.addEventListener('resize', adjustMainHeight);
</script>
</body>
</html>
