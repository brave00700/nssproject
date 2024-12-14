<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pdf_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT name, file FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fileName, $fileData);
    $stmt->fetch();

    if ($fileName) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        echo $fileData;
    } else {
        echo "File not found.";
    }

    $stmt->close();
}

$conn->close();
?>
