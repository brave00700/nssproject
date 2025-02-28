<?php
$id = $_GET['id']; // Get file ID from request
$conn = new mysqli("localhost", "root", "", "nss_db");
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
