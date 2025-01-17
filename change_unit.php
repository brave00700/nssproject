<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nss_application";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted for updating units
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_unit'], $_POST['register_no'])) {
    $selectedStudents = $_POST['register_no'];
    $newUnit = intval($_POST['new_unit']);

    // Validate input
    if (empty($selectedStudents) || $newUnit < 1 || $newUnit > 5) {
        echo "<script>alert('Invalid data. Please try again.'); window.location.href = 'view_admitted_students.php';</script>";
        exit();
    }

    // Update units for selected students
    $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
    $sql = "UPDATE admitted_students SET Unit = ? WHERE Register_no IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    // Dynamically bind parameters
    $types = str_repeat('s', count($selectedStudents)); // 's' for each student ID
    $stmt->bind_param("i" . $types, $newUnit, ...$selectedStudents);

    if ($stmt->execute()) {
        echo "<script>alert('Unit updated successfully for selected students.'); window.location.href = 'view_admitted_students.php';</script>";
    } else {
        echo "<script>alert('Error updating unit. Please try again.'); window.location.href = 'view_admitted_students.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit();
}

// If students are selected for unit change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_no'])) {
    $selectedStudents = $_POST['register_no'];

    // Get details of selected students
    $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
    $sql = "SELECT Register_no, Name, Unit FROM admitted_students WHERE Register_no IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($selectedStudents)), ...$selectedStudents);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();
} else {
    // Redirect back if no students were selected
    header("Location: view_admitted_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Unit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main-container">
    <h1>Change Unit for Selected Students</h1>
    <form action="" method="POST">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Current Unit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['Register_no']) ?></td>
                    <td><?= htmlspecialchars($student['Name']) ?></td>
                    <td><?= htmlspecialchars($student['Unit']) ?></td>
                </tr>
                <!-- Hidden input to pass student register numbers -->
                <input type="hidden" name="register_no[]" value="<?= htmlspecialchars($student['Register_no']) ?>">
            <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <label for="unit">Select New Unit:</label>
        <select name="new_unit" id="unit" required>
            <option value="" disabled selected>Select Unit</option>
            <option value="1">Unit 1</option>
            <option value="2">Unit 2</option>
            <option value="3">Unit 3</option>
            <option value="4">Unit 4</option>
            <option value="5">Unit 5</option>
        </select>
        <br><br>
        <button type="submit" class="submit-button">Update Unit</button>
        <a href="view_admitted_students.php" class="cancel-button">Cancel</a>
    </form>
</div>
</body>
</html>
