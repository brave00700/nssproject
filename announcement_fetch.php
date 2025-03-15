<?php
$id = $_GET['id']; // Get file ID from request
require_once "config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$stmt = $conn->prepare("SELECT id, name, file FROM announcements WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result_student = $stmt->get_result();
$file = $result_student->fetch_assoc();

if ($file) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($file['file']);

    // Set headers
    header("Content-Type: " . $mime);
    echo $file['file']; // Output file content
    exit;
} else {
    echo "File not found.";
}
?>
