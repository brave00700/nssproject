<?php 
    include "exe_header.php";
    session_start();

    require_once __DIR__ . "/../config_db.php";

    // Load the environment variables
    loadEnv(__DIR__ . '/../.env');

    // Fetch environment variables
    $DB_HOST = getenv("DB_HOST");
    $DB_USER = getenv("DB_USER");
    $DB_PASS = getenv("DB_PASS");
    $DB_NAME = getenv("DB_NAME");

    // Checking session timeout
    if (isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']) {
        session_unset();
        session_destroy();
        header("Location: exec_login.php");
        exit();
    }
    $_SESSION['last_seen'] = time();

    // Check if executive is logged in
    if (!isset($_SESSION['exec_id'])) {
        header("Location: exec_login.php");
        exit();
    }

    $exec_id = $_SESSION['exec_id'];
    $unit = $_SESSION['unit'];

    // Database connection
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission to add MoM
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $meeting_date = $_POST['meeting_date'];
        $time = $_POST['time'];
        $venue = $_POST['venue'];
        $attendees = $_POST['attendees'];
        $recorder = $_POST['recorder'];
        $agenda = $_POST['agenda'];
        $discussion = $_POST['discussion'];

        $stmt = $conn->prepare("INSERT INTO mom_records (meeting_date, time, venue, attendees, recorder, agenda, discussion,Unit)
            VALUES (?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("sssssssi", $meeting_date, $time, $venue, $attendees, $recorder, $agenda, $discussion,$unit);
        $stmt->execute();
        $stmt->close();
    }

    // Fetch MoM records for logged-in executive
    $search = isset($_GET['search']) ? $_GET['search'] : ""; // Define $search variable
$search_param = "%$search%";

$stmt = $conn->prepare("SELECT * FROM mom_records WHERE unit = ? AND (meeting_date LIKE ? OR venue LIKE ?) ORDER BY meeting_date DESC");
$stmt->bind_param("sss", $unit, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result(); // Store the query result

    
?>

<style>
/* General Styles */
*{
    box-sizing: border-box;
}
body {
    font-family: Arial, sans-serif;
}
.widget, .about_main_divide {
    overflow: auto;
}


.top-bar {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 5px;
}

input, textarea, button {
    padding: 8px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
}

button {
    cursor: pointer;
    background: #007bff;
    color: white;
    border: none;
    padding: 10px;
}

button:hover {
    background: #0056b3;
}



/* General Table Styling */
#momTable {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed; /* Ensures proper alignment */
}

/* Header and Cell Styling */
#momTable th, #momTable td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top; /* Ensures content stays aligned properly */
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal; /* Allows wrapping */
}

/* Set Specific Column Widths for Better Alignment */
#momTable th:nth-child(1), 
#momTable td:nth-child(1) {
    width: 50px; /* Sl No */
    text-align: center;
}

#momTable th:nth-child(2), 
#momTable td:nth-child(2) {
    width: 120px; /* Date */
}

#momTable th:nth-child(3), 
#momTable td:nth-child(3) {
    width: 100px; /* Time */
}

#momTable th:nth-child(4), 
#momTable td:nth-child(4) {
    width: 150px; /* Venue */
}

#momTable th:nth-child(5), 
#momTable td:nth-child(5) {
    width: 80px; /* Unit */
    text-align: center;
}

/* Allow Text Wrapping for Longer Fields */
#momTable th:nth-child(6), 
#momTable td:nth-child(6),
#momTable th:nth-child(7), 
#momTable td:nth-child(7),
#momTable th:nth-child(8), 
#momTable td:nth-child(8),
#momTable th:nth-child(9), 
#momTable td:nth-child(9),
#momTable th:nth-child(10), 
#momTable td:nth-child(10) {
    width: 200px; /* Adjust width for text-heavy fields */
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Action Links Column */
#momTable th:nth-child(11), 
#momTable td:nth-child(11) {
    width: 120px;
    text-align: center;
}

/* Ensure Table is Scrollable */
.table-container {
    width: 100%;
    max-width: 100%;
    max-height: 400px;
    overflow-x: auto; /* Allows horizontal scrolling */
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 5px;
}




.no-records {
    text-align: center;
    color: red;
}

.success-msg {
    color: green;
    text-align: center;
}

.error-msg {
    color: red;
    text-align: center;
}

.action-links a {
    text-decoration: none;
    color: blue;
    font-weight: bold;
    margin-right: 10px;
}
.discussion {
    width: 250px; /* Adjust width as needed */
    word-wrap: break-word;
    overflow-wrap: break-word;
    display: block;
    white-space: normal;
    line-height: 1.5; /* Space between lines */
}

.discussion::after {
    content: "";
    display: block;
    width: 100%;
    height: 1px;
    visibility: hidden;
}

/* Adjust the form container */
form {
    max-width: 800px;
    margin: 0 auto;
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Grid layout */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px; /* Space between columns */
}

/* Ensure input and textarea fields are properly sized */
form input, 
form textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: white;
}

/* Full-width fields for textareas */
.full-width {
    grid-column: span 2;
}

/* Ensure no overflow hides the right side */
input, textarea {
    overflow: visible;
}

.form-grid div{
    padding:5px;
}

</style>

<div class="main">
    <div class="about_main_divide">
        <!-- Left navigation -->
        <div class="about_nav">
            <ul>
                <li><a href="exe_stock.php">Stock</a></li>
                <li><a href="exe_budget.php">Budget/Finance</a></li>
                <li><a href="exe_indent.php">Indent Records</a></li>
                <li><a class="active" href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a href="exe_work_done.php">Work Done Diary</a></li>
            </ul>
        </div>
        
        <!-- Main content -->
        <div class="widget">
            <h2>Minutes of Meeting</h2>

            <!-- Form to add MoM -->
            <form action="" method="POST">
    <h3>Add New MoM</h3>
    <div class="form-grid">
        <div>
            <label>Date:</label>
            <input type="date" name="meeting_date" required>
        </div>
        
        <div>
            <label>Time:</label>
            <input type="time" name="time" required>
        </div>
        
        <div>
            <label>Venue:</label>
            <input type="text" name="venue" required>
        </div>
        
        <div>
            <label>Minutes Recorded By:</label>
            <input type="text" name="recorder" required>
        </div>

        <div class="full-width">
            <label>Attendees:</label>
            <textarea name="attendees" required></textarea>
        </div>
        
        <div class="full-width">
            <label>Agenda:</label>
            <textarea name="agenda" required></textarea>
        </div>

        <div class="full-width">
            <label>Discussion & Decisions:</label>
            <textarea name="discussion" required></textarea>
        </div>
        
        <div class="full-width">
            <button type="submit">Save Minutes</button>
        </div>
    </div>
</form>


            <!-- Search bar -->
            <div class="top-bar">
                <input type="text" id="searchBar" placeholder="Search by Date or Venue..." onkeyup="searchTable()">
            </div>

            <!-- Table to display MoM records -->
            <div class="table-container">
    <table id="momTable">
        <tr>
            <th>Sl No</th>
            <th>Date</th>
            <th>Time</th>
            <th>Venue</th>
            <th>Unit</th>
            <th>Attendees</th>
            <th>Agenda</th>
            <th>Recorded By</th>
            <th>Discussion</th>
            <th>Decisions</th>
            <th>Actions</th>
        </tr>
        <?php 
        $sl = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sl++; ?></td>
                    <td class="meeting-date"><?= $row['meeting_date']; ?></td>
                    <td class="time"><?= $row['time']; ?></td>
                    <td class="venue"><?= $row['venue']; ?></td>
                    <td class="unit"><?= $row['Unit']; ?></td>
                    <td class="attendees"><?= nl2br($row['attendees']); ?></td>
                    <td class="agenda"><?= nl2br($row['agenda']); ?></td>
                    <td><?= $row['recorder']; ?></td>
                    <td class="discussion"><?= nl2br($row['discussion']); ?></td>
                    <td class="decisions"><?= nl2br($row['decisions']); ?></td>
                    <td class="action-links">
                        <a href="edit_mom.php?id=<?= $row['id']; ?>">✏️ Edit</a> |
                        <a href="exe_delete_mom.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                    </td>
                </tr>
        <?php } 
        } else {
            echo "<tr><td colspan='11' class='no-records'>No Meeting Records Found</td></tr>";
        }
        ?>
    </table>
</div>

        </div>
    </div>
</div>

<script>
// Search Functionality
function searchTable() {
    var input = document.getElementById('searchBar').value.toLowerCase();
    var table = document.getElementById('momTable');
    var tr = table.getElementsByTagName('tr');

    for (var i = 1; i < tr.length; i++) {
        var tdDate = tr[i].getElementsByClassName('meeting-date')[0];
        var tdVenue = tr[i].getElementsByClassName('venue')[0];

        if (tdDate && tdVenue) {
            var textDate = tdDate.textContent.toLowerCase();
            var textVenue = tdVenue.textContent.toLowerCase();

            if (textDate.includes(input) || textVenue.includes(input) || input === "") {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
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

<?php 
    $stmt->close();
    $conn->close();
?>
