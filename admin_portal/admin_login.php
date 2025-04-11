<?php
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

$message = '';

// Checking for admin login
if(isset($_POST['login'])){
    if (!empty($_POST['id']) && !empty($_POST['pass'])){
        $admin_id = $_POST['id'];
        $admin_pass = $_POST['pass'];

        // Create a connection object
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT role, password FROM staff WHERE User_id = ?");
        $stmt->bind_param("s", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $admin_data = $result->fetch_assoc();
            if($admin_data['password'] == $admin_pass && strtolower($admin_data['role']) == 'admin'){
                $_SESSION['admin_id'] = $admin_id;
                header("Location: manage_applications.php");
                exit();
            } else {
               
                echo "<script>alert('Invalid credentials or role mismatch. Access restricted to Admins.');</script>";
            }
        } else {
            
            echo "<script>alert('Invalid Admin ID or Password');</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
       
        echo "<script>alert('Please enter both Admin ID and Password');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    form {
        background-color: #ffffff; /* White background */
        padding: 1.5rem; /* Reduced padding inside the form */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
        width: 350px; /* Reduced form width */
        margin: auto; /* Center horizontally */
    }

    table {
        width: 100%;
    }

    .label {
        font-size: 1rem;
        color: #333;
        text-align: right; /* Align text to the right */
        padding-right: 0.8rem; /* Reduced right padding */
    }

    input {
        width: 100%; /* Full width for consistency */
        padding: 0.6rem; /* Reduced spacing inside inputs */
        margin-bottom: 0.8rem; /* Reduced space between inputs */
        border: 1px solid #ccc; /* Light gray border */
        border-radius: 6px; /* Slightly rounded corners for inputs */
        font-size: 1rem;
    }

    button {
        width: 100%; /* Button spans the form width */
        padding: 0.6rem; /* Reduced padding for the button */
        font-size: 1rem;
        font-weight: bold; /* Bold text for emphasis */
        color: #fff; /* White text */
        background-color: #ffa200; /* Orange background */
        border: none; /* Remove default border */
        border-radius: 6px; /* Slightly rounded corners */
        cursor: pointer; /* Pointer cursor on hover */
        transition: all 0.3s ease; /* Smooth transition for hover */
    }

    button:hover {
        background-color: #e69202; /* Darker orange on hover */
    }

    button:active {
        background-color: #cc7d02; /* Even darker orange on click */
        transform: scale(0.98); /* Slightly shrink on click */
    }

    .main {
        display: flex;
        justify-content: center; /* Center form horizontally */
        align-items: center; /* Center form vertically */
        height: 60vh; /* Reduced height of container */
        background-color: #f7f7f7; /* Light gray background */
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
      <div class="header-subtext">ADMIN PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    <ul>
        <li><a class="active" href="">Log In</a></li>
    </ul>
</div>

<div class="main">
    <form method="post">
        <table>
            <tr>
                <td class="label">Admin ID</td>
                <td><input type="text" name='id' required></td>
            </tr>
            <tr>
                <td class="label">Password</td>
                <td><input type="password" name='pass' required></td>
            </tr>
            <tr>
                <td></td>
                <td><button name="login" type="submit">Login</button></td>
            </tr>
        </table>
    </form>
    <?php

    
    ?>
</div>
<script src="script.js"></script>
</body>
</html>
