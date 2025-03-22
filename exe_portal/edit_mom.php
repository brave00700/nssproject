<?php
    include "exe_header.php";
    session_start();

    if (!isset($_SESSION['exec_id'])) {
        header("Location: exec_login.php");
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "nss_db");
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

<?php
    $stmt->close();
    $conn->close();
?>
