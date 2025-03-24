<?php 
include "exe_header.php";
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

$exec_id = $_SESSION['exec_id'];
$unit = $_SESSION['unit'];
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed. Please try again later.</p>");
}

// Handle report upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report'])) {
    $file_name = $_FILES['report']['name'];
    $file_tmp = $_FILES['report']['tmp_name'];
    $file_size = $_FILES['report']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf'];

    if (!in_array($file_ext, $allowed_extensions)) {
        echo "<p style='color:red;'>Invalid file type! Only PDFs (.pdf) are allowed.</p>";
    } elseif ($file_size > 2 * 1024 * 1024) {
        echo "<p style='color:red;'>File size must be under 2MB.</p>";
    } else {
        // Preserve the original file name
        $new_file_name = basename($file_name); 
        $target_path = "../assets/uploads/reports/" . $new_file_name;
        if (!is_dir("../assets/uploads/reports/")) {
            mkdir("../assets/uploads/reports/", 0777, true); // Create directory if not exists
        }
    
        // Move the uploaded file
        if (move_uploaded_file($file_tmp, $target_path)) {
            // Store the original file name in the database
            $stmt = $conn->prepare("INSERT INTO work_reports (exec_id, wr_file, wr_status, upload_date, unit) VALUES (?, ?, 'Uploaded', NOW(), ?)");
            $stmt->bind_param("sss", $exec_id, $new_file_name, $unit);
            $stmt->execute();
    
            echo "<p style='color:green;'>Report uploaded successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error uploading the file.</p>";
        }
    }
}

// Handle report deletion
if (isset($_GET['delete'])) {
    $wr_id = intval($_GET['delete']);

    // Fetch file name before deleting
    $stmt = $conn->prepare("SELECT wr_file FROM work_reports WHERE wr_id = ? AND exec_id = ? AND unit = ?");
    $stmt->bind_param("iis", $wr_id, $exec_id, $unit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $report = $result->fetch_assoc();
        $file_path = "../assets/uploads/reports/" . $report['wr_file'];

        // Delete file from server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete record from database
        $stmt = $conn->prepare("DELETE FROM work_reports WHERE wr_id = ? AND exec_id = ? AND unit = ?");
        $stmt->bind_param("iis", $wr_id, $exec_id, $unit);
        $stmt->execute();
        
        echo "<p style='color: red;'>Report deleted successfully.</p>";
    }
}
?>

<div class="main">
    <div class="about_main_divide">

        <section class="widget">
            <h2>Upload Work Report</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="file" name="report" required>
                <button type="submit">Upload</button>
            </form>

            <h2>Uploaded Reports</h2>
            <div class="grid-container">
                <?php
                $report_query = "SELECT wr_id, wr_file FROM work_reports WHERE exec_id = ? ORDER BY upload_date DESC";
                $stmt = $conn->prepare($report_query);
                $stmt->bind_param("i", $exec_id);
                $stmt->execute();
                $report_result = $stmt->get_result();

                if ($report_result->num_rows > 0) {
                    while ($report = $report_result->fetch_assoc()) {
                        echo "<div class='grid-item'>
                                <a href='../assets/uploads/reports/" . $report['wr_file'] . "' target='_blank'>
                                    <img src='do_ic.png' alt='Document' class='doc-icon'>
                                    <p>" . htmlspecialchars($report['wr_file']) . "</p>
                                </a>
                                <br>
                                <a href='?delete=" . $report['wr_id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this report?\");'>Delete</a>
                              </div>";
                    }
                } else {
                    echo "<p>No reports uploaded yet.</p>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </div>
        </section>
    </div>
</div>

<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 20px;
    }
    .grid-item {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        position: relative;
    }
    .grid-item a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
    }
    .doc-icon {
        width: 50px;
        height: 50px;
        margin-bottom: 5px;
    }
    .delete-btn {
        display: inline-block;
        margin-top: 8px;
        color: red;
        font-weight: bold;
        cursor: pointer;
        border: none;
        background: none;
    }
</style>
