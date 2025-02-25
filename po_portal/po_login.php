<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Officer Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
        <b style="font-size: 1.3rem">Program Officer Portal</b><br>
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
                <td class="label">Officer ID</td>
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
    // Starting the session
    session_start();

    // Checking for program officer login
    if(isset($_POST['login'])){
        if (!empty($_POST['id']) && !empty($_POST['pass'])){
            $po_id = $_POST['id'];
            $officer_pass = $_POST['pass'];

            // Create a connection object
            $conn = new mysqli("localhost", "root", "", "nss_db");
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT role, password, unit FROM staff WHERE user_id = ?");
            $stmt->bind_param("s", $po_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                $officer_data = $result->fetch_assoc();
                if($officer_data['password'] == $officer_pass && strtolower($officer_data['role']) == 'po'){
                    $_SESSION['po_id'] = $po_id;
                    $_SESSION['unit'] = intval($officer_data['unit']);
                    
                    header("Location: po_profile.php");
                    exit();
                } else {
                    echo 'Invalid credentials or role mismatch. Access restricted to Program Officers.';
                }
            } else {
                echo 'Invalid Officer ID or Password';
            }

            $stmt->close();
            $conn->close();
        } else {
            echo 'Please enter both Officer ID and Password';
        }
    }
    ?>
</div>
<script src="script.js"></script>
</body>
</html>