<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nss_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['reg']; // Student ID from session
$sql = "UPDATE student_notifications SET status = 'READ' WHERE student_id = ? AND status = 'UNREAD'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["message" => "Notifications marked as read"]);
?>
