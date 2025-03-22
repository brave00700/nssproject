<?php 
    include "exe_header.php";
    session_start();

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
$conn = new mysqli("localhost", "root", "", "nss_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $event_name = $_POST["event_name"];

    // File details
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    $file_size = $_FILES["file"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validate file type (Only PDF)
    if ($file_ext !== "pdf") {
        echo "<p class='error-msg'>❌ Only PDF files are allowed.</p>";
    } elseif ($file_size > 5 * 1024 * 1024) { // Max 5MB
        echo "<p class='error-msg'>❌ File size should be less than 5MB.</p>";
    } else {
        // Upload directory
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Save file with a unique name
        $file_path = $upload_dir . uniqid() . "_" . basename($file_name);
        move_uploaded_file($file_tmp, $file_path);
        

        // Store file in database
        $stmt = $conn->prepare("INSERT INTO budget (event_name, pdf_file, status, uploaded_at,unit) VALUES (?, ?, 'Pending', NOW(), ?)");
        $stmt->bind_param("ssi", $event_name, $file_path,$unit);
        
        if ($stmt->execute()) {
            echo "<p class='success-msg'>✅ Budget PDF uploaded successfully!</p>";
        } else {
            echo "<p class='error-msg'>❌ Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 5px;
}

input, select, button {
    padding: 8px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    cursor: pointer;
    background: #007bff;
    color: white;
    border: none;
}

button:hover {
    background: #0056b3;
}

.upload-container {
    display: none;
    padding: 15px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    border-radius: 5px;
}

.upload-btn {
    background: green;
}

.cancel-btn {
    background: red;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background: #007bff;
    color: white;
}

tr:nth-child(even) {
    background: #f2f2f2;
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

.view-btn {
    text-decoration: none;
    color: blue;
    font-weight: bold;
}
</style>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a href="exe_stock.php">Stock</a></li>
                <li><a class="active" href="exe_budget.php">Budget/Finance</a></li>
                <li><a href="exe_indent.php">Indent Records</a></li>
                <li><a href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a href="exe_work_done.php">Work Done Diary</a></li>
            </ul>
        </div>

        <div class="widget">
            <div class="top-bar">
                <input type="text" id="searchBar" placeholder="Search by Event Name..." onkeyup="searchTable()">
                <input type="date" id="dateFilter" onchange="searchTable()">
                <button id="addBtn" onclick="showUploadForm()">Add Budget</button>
            </div>

            <!-- Upload Form -->
            <div id="uploadForm" class="upload-container">
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="event_name">Event Name:</label>
                    <input type="text" name="event_name" required>

                    <label for="file">Upload PDF:</label>
                    <input type="file" name="file" accept=".pdf" required>

                    <button type="submit" class="upload-btn">Upload</button>
                    <button type="button" class="cancel-btn" onclick="hideUploadForm()">Cancel</button>
                </form>
            </div>

            <!-- Budget Table -->
            <?php
$stmt = $conn->prepare("SELECT id, event_name, pdf_file, status, uploaded_at FROM budget WHERE unit = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $unit);
$stmt->execute();
$result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table id='budgetTable'>
                        <tr>
                            <th>Sl No</th>
                            <th>Event</th>
                            <th>Budget File</th>
                            <th>Status</th>
                            <th>Uploaded At</th>
                        </tr>";

                $sl = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$sl}</td>
                            <td class='event-name'>{$row['event_name']}</td>
                            <td><a href='{$row['pdf_file']}' target='_blank' class='view-btn'>📄 View PDF</a></td>
                            <td>{$row['status']}</td>
                            <td class='event-date'>{$row['uploaded_at']}</td>
                          </tr>";
                    $sl++;
                }
                echo "</table>";
            } else {
                echo "<p class='no-records'>No Budget Records Found</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</div>

<script>
// Show upload form
function showUploadForm() {
    document.getElementById('uploadForm').style.display = 'block';
}

// Hide upload form
function hideUploadForm() {
    document.getElementById('uploadForm').style.display = 'none';
}

// Search Functionality (Event Name & Date)
function searchTable() {
    var inputName = document.getElementById('searchBar').value.toLowerCase();
    var inputDate = document.getElementById('dateFilter').value;
    var table = document.getElementById('budgetTable');
    var tr = table.getElementsByTagName('tr');

    for (var i = 1; i < tr.length; i++) {
        var tdName = tr[i].getElementsByClassName('event-name')[0];
        var tdDate = tr[i].getElementsByClassName('event-date')[0];

        if (tdName && tdDate) {
            var eventName = tdName.textContent.toLowerCase();
            var eventDate = tdDate.textContent.split(' ')[0];

            if (
                (eventName.includes(inputName) || inputName === "") &&
                (eventDate === inputDate || inputDate === "")
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

</body>
</html>
