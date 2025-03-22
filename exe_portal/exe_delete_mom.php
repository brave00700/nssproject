<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nss_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: exe_mom.php");
    exit();
}

$id = $_GET['id'];

// Delete the record
$stmt = $conn->prepare("DELETE FROM mom_records WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: exe_mom.php");
exit();
?>
