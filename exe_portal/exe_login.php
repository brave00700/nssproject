<?php
// Start session
session_start();

require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
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
            background-color:rgb(239, 175, 15);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color:rgb(247, 172, 23);
        }

        button:active {
            background-color:rgb(248, 173, 12);
            transform: scale(0.98);
        }

        .main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60vh;
            background-color: #f7f7f7;
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
        <li><a class="active" href="">Log In</a></li>
    </ul>
</div>

<div class="main">
    <form method="post">
        <table>
            <tr>
                <td class="label">User ID</td>
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
    

    if (isset($_POST['login'])) {
        if (!empty($_POST['id']) && !empty($_POST['pass'])) {
            $exec_id = $_POST['id'];
            $exec_pass = $_POST['pass'];

            // Connect to database
            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT user_id, password, role, unit FROM staff WHERE user_id = ?");
            $stmt->bind_param("s", $exec_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $cred = $result->fetch_assoc();
                if ($cred['password'] == $exec_pass) {
                    $_SESSION['exec_id'] = $exec_id;
                    $_SESSION['role'] = $cred['role'];
                    $_SESSION['last_seen'] = time();
                    $_SESSION['timeout'] = 18000;
                    $_SESSION['unit'] = $cred['unit'];

                    header("Location: exe_profile.php");
                    exit();
                } else {
                    echo '<p style="color:red; text-align:center;">Invalid Password</p>';
                }
            } else {
                echo '<p style="color:red; text-align:center;">Invalid User ID</p>';
            }

            $stmt->close();
            $conn->close();
        } else {
            echo '<p style="color:red; text-align:center;">Please enter both User ID and Password</p>';
        }
    }
    ?>
</div>
<script src="script.js"></script>
</body>
</html>
