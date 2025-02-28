<?php

$conn = new mysqli("localhost", "root", "", "nss_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT credits, hours, credits.register_no, students.name, students.unit 
FROM credits 
JOIN students on credits.register_no = students.user_id
WHERE credits.register_no = ?");
$stmt->bind_param("s", $reg);
$stmt->execute();
$result3 = $stmt->get_result();

if($result3->num_rows > 0){
    $row = $result3->fetch_assoc();
    $hours = $row['hours'];
    $credits = $row['credits'];
    $name = $row['name'];
    $unit = $row['unit'];
}
?>