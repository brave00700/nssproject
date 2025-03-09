<?php
session_start();
$reg = $_SESSION['reg'];
$conn = new mysqli("localhost", "root", "", "nss_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 // Student ID from session
$sql = "SELECT notice_id, notice, created_at, status FROM student_notifications WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $reg);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($notifications);
?>