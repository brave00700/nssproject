<?php
session_start();

require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

if (!isset($_SESSION['exec_id'])) {
    header("Location: exec_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report'])) {
    $event_id = $_POST['event_id'];
    $exec_id = $_SESSION['exec_id'];
    
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $uploadDir = "../reports/";
    $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    $fileName = basename($_FILES['report']['name']);
    $fileType = $_FILES['report']['type'];
    $fileTmp = $_FILES['report']['tmp_name'];
    $fileSize = $_FILES['report']['size'];
    
    if (!in_array($fileType, $allowedTypes)) {
        die("<p style='color:red;'>Invalid file type. Only PDF and DOCX allowed.</p>");
    }
    
    if ($fileSize > 5 * 1024 * 1024) {
        die("<p style='color:red;'>File size exceeds 5MB limit.</p>");
    }
    
    $newFileName = "report_{$event_id}_" . time() . "_" . $fileName;
    $targetFile = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTmp, $targetFile)) {
        $stmt = $conn->prepare("UPDATE events SET report_file = ?, report_status = 'Pending' WHERE event_id = ?");
        $stmt->bind_param("si", $newFileName, $event_id);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Report uploaded successfully.</p>";
        } else {
            echo "<p style='color:red;'>Database update failed.</p>";
        }
        
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Error uploading the file.</p>";
    }
    
    $conn->close();
    header("Location: exec_profile.php");
    exit();
} else {
    echo "<p style='color:red;'>No file uploaded.</p>";
}
