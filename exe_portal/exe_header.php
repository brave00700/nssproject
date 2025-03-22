<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
    .top-bar {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .top-bar input[type="text"] {
      padding: 8px;
      margin-right: 10px;
      width: 200px;
    }
    .top-bar select {
      padding: 8px;
      margin-right: 10px;
    }
    .top-bar button {
      padding: 8px 12px;
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
            <li><a class="<?php echo (basename($_SERVER['PHP_SELF']) == 'exe_register.php' || basename($_SERVER['PHP_SELF']) == 'exe_budget.php') ? 'active' : ''; ?>" href="exe_register.php">Register</a></li>
            <li><a class="<?php echo (basename($_SERVER['PHP_SELF']) == 'exe_report.php') ? 'active' : ''; ?>" href="exe_report.php">Reports</a></li>
            <li><a class="<?php echo (basename($_SERVER['PHP_SELF']) == 'exe_profile.php') ? 'active' : ''; ?>" href="exe_profile.php">Profile</a></li>
    </ul>
    
</div>