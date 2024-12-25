<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Student Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../nss_logo.png" alt="logo" />
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
                        <td class="label">Register No/NSS-ID</td>
                        <td><input type="text" name='id'></td>
                    </tr>
                    <tr>
                        <td class="label">Password</td>
                        <td><input type="password" name='pass'></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button name="login" type="submit">Login</button></td>
                    </tr>
                </table>
            </form>
            <?php
            // Creating a new session
            session_start();

            
            
            // Checking for login
            if(isset($_POST['login'])){
                if (!empty($_POST['id']) && !empty($_POST['pass'])){
                    $lreg = $_POST['id'];
                    $lpass = $_POST['pass'];

                    // Create a connection object
                    $conn = new mysqli("localhost", "root", "", "nss_application");
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("SELECT user_id, password FROM admitted_students WHERE user_id = ?");
                    $stmt->bind_param("s", $lreg);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if($result->num_rows > 0) {
                        $cred = $result->fetch_assoc();
                        if($cred['password'] == $lpass){
                            $_SESSION['reg'] = $lreg;
                            header("Location: std_profile.php");
                            exit();
                        }
                    }
                    else {
                        echo 'Invalid UserID or Password';
                    }

                    $stmt->close();
                    $conn->close();
                }else{
                    echo 'Please enter both UserID and Password';
                }
            }
?>
</div>
</body>
</html>
