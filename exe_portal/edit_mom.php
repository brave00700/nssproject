<?php
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

    include "exe_header.php";
    session_start();

    if (!isset($_SESSION['exec_id'])) {
        header("Location: exec_login.php");
        exit();
    }

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid MoM ID.");
    }
    
    $mom_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM mom_records WHERE id = ?");
    $stmt->bind_param("i", $mom_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mom = $result->fetch_assoc();
    
    if (!$mom) {
        die("MoM record not found.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $meeting_date = $_POST['meeting_date'];
        $time = $_POST['time'];
        $venue = $_POST['venue'];
        $attendees = $_POST['attendees'];
        $recorder = $_POST['recorder'];
        $agenda = $_POST['agenda'];
        $discussion = $_POST['discussion'];
        $decisions = $_POST['decisions'];
    
        $update_stmt = $conn->prepare("UPDATE mom_records SET meeting_date=?, time=?, venue=?, attendees=?, recorder=?, agenda=?, discussion=?, decisions=? WHERE id=?");
        $update_stmt->bind_param("ssssssssi", $meeting_date, $time, $venue, $attendees, $recorder, $agenda, $discussion, $decisions, $mom_id);
    
        if ($update_stmt->execute()) {
            echo "<p style='color:green;'>MoM updated successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error updating MoM.</p>";
        }
    }
    $stmt->close();
    $conn->close();
?>

<div class="main">
    <h2>Edit Minutes of Meeting</h2>
    <form action="" method="POST">
        <label>Date:</label>
        <input type="date" name="meeting_date" value="<?php echo $mom['meeting_date']; ?>" required>

        <label>Time:</label>
        <input type="time" name="time" value="<?php echo $mom['time']; ?>" required>

        <label>Venue:</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($mom['venue']); ?>" required>

        <label>Attendees:</label>
        <textarea name="attendees" required><?php echo htmlspecialchars($mom['attendees']); ?></textarea>

        <label>Minutes Recorded By:</label>
        <input type="text" name="recorder" value="<?php echo htmlspecialchars($mom['recorder']); ?>" required>

        <label>Agenda:</label>
        <textarea name="agenda" required><?php echo htmlspecialchars($mom['agenda']); ?></textarea>

        <label>Discussion & Decisions:</label>
        <textarea name="discussion" required><?php echo htmlspecialchars($mom['discussion']); ?></textarea>

        <label>Final Decisions:</label>
        <textarea name="decisions" required><?php echo htmlspecialchars($mom['decisions']); ?></textarea>

        <button type="submit">Update MoM</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        let isValid = true;

        // Get input values
        const meetingDate = new Date(document.querySelector("input[name='meeting_date']").value);
        const currentDate = new Date();
        const maxDate = new Date();
        maxDate.setDate(currentDate.getDate() + 30);

        currentDate.setHours(0, 0, 0, 0);
        meetingDate.setHours(0, 0, 0, 0);
        maxDate.setHours(0, 0, 0, 0);

        // Validate date (should be today or within 30 days)
        if (meetingDate < currentDate || meetingDate > maxDate) {
            alert("Meeting date should be today or within the next 30 days.");
            isValid = false;
        }

        // Validate time (should be between 9 AM and 6 PM)
        const timeInput = document.querySelector("input[name='time']").value;
        const [hours, minutes] = timeInput.split(":").map(Number);
        if (hours < 9 || hours > 18 || (hours === 18 && minutes > 0)) {
            alert("Meeting time should be between 9:00 AM and 6:00 PM.");
            isValid = false;
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
});
</script>
