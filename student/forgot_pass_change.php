<?php
// Creating a new session
session_start();

parse_str($_SERVER['QUERY_STRING'], $query_params);
$token = $query_params['token'];

$conn = new mysqli("localhost", "root", "", "nss_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
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
        $_SESSION['valid_reset'] = true;
    }else{
        $_SESSION['valid_reset'] = false;
    }
}else{
    $_SESSION['valid_reset'] = false;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Student</title>
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
    p.msg {
        width: 350px;
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        background-color: #ffb1005c; 
        color:rgb(255, 0, 0);
        font-weight: 700;
        padding: 1rem;   
        text-align: center;
        margin: 20px auto;
    }

</style>
</head>
<body>
<?php include "header.php" ?>
   
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
                        <td class="label">New Password</td>
                        <td><input type="password" name='pass1' ></td>
                    </tr>
                    <tr>
                        <td class="label">Confirm Password</td>
                        <td><input type="password" name='pass2' ></td>
                    </tr>
                        <td></td>
                        <td><button type="submit" name="reset">Submit</button></td>
                    </tr>
                </table>
            </form>
            <?php
            

            // Checking for request
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])){
                $pass1 = $_POST['pass1'];
                $pass2 = $_POST['pass2'];
                if (empty($_POST['pass1']) || empty($_POST['pass2'])){
                    echo '<p class="msg">Please enter both passwords</p>';
                }
                else if($pass1 != $pass2){
                    echo "<p class='msg'>Passwords don't match</p>";
                }
                else{
                    // Create a connection object
                    $conn = new mysqli("localhost", "root", "", "nss_db");
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("UPDATE students SET password = ?, login_attempts = 0 WHERE user_id = ?");
                    $stmt->bind_param("ss", $pass1, $user_id);
                    if($stmt->execute()){
                        echo "<p class='msg'>Password changed successfully</p>";

                        $stmt2 = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
                        $stmt2->bind_param("s", $user_id);
                        if(!$stmt2->execute()){
                            echo "Error occurred" . $conn->connect_error;
                        }

                    }
                    $stmt->close();
                    $conn->close();
                }
            }
?>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>


<?php
if(!$_SESSION['valid_reset']){
    echo "<script>document.querySelector('.main').innerHTML = 'Invalid Reset Token';</script>";
}
?>