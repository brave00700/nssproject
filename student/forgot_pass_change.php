<?php
require_once __DIR__ . "/functions.php";
// Creating a new session
session_start();

$hideNotifications = true;

parse_str($_SERVER['QUERY_STRING'], $query_params);
$token = $query_params['token'];

$conn = getDatabaseConnection();

$user_id = '';
if(isValidToken($conn, $token, $user_id)){
    $_SESSION['valid_reset'] = true;
}else{
    $_SESSION['valid_reset'] = false;
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
    width: 90%; 
    max-width: 400px; 
    margin: auto; 
}

.nav > ul {
    display: block;
}
table {
    width: 100%;
    display: flex;
    justify-content: center;
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
    justify-content: flex-start; 
    align-items: center; 
    background-color: #f7f7f7; 
    padding: 20px;
    min-height: 100vh;
}

p.msg {
    width: 90%;
    max-width: 350px;
    border-radius: 8px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    background-color: #ffb1005c; 
    color: rgb(255, 0, 0);
    font-weight: 700;
    padding: 1rem;   
    text-align: center;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    form {
        width: 100%;
        max-width: 350px;
    }

    .label {
        text-align: left;
        display: block;
        padding-bottom: 5px;
    }

    tr {
        display: flex;
        flex-direction: column;
    }

    input {
        font-size: 0.9rem;
        padding: 0.5rem;
    }

    button {
        font-size: 0.9rem;
        padding: 0.5rem;
    }
}

@media screen and (max-width: 480px) {
    form {
        padding: 1rem;
    }

    .main {
        padding: 10px;
        box-shadow: none;
    }

    .label {
        font-size: 0.9rem;
    }

    input, button {
        font-size: 0.85rem;
        padding: 0.5rem;
    }

    p.msg {
        font-size: 0.9rem;
    }
}

</style>
</head>
<body>
<?php include "header.php" ?>
   
<div class="nav">
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

                $message = resetPassword($conn, $pass1, $pass2, $user_id);
                echo "<p class='msg'>$message</p>";
                
            }
            $conn->close();
?>
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


<?php
if(!$_SESSION['valid_reset']){
    echo "<script>document.querySelector('.main').innerHTML = 'Invalid Reset Token';</script>";
}
?>