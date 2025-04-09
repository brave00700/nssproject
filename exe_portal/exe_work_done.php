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

// Redirect if not logged in
if (!isset($_SESSION['exec_id'])) {
    header("Location: exec_login.php");
    exit();
}

$exec_id = $_SESSION['exec_id'];
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("<p class='error-msg'>Connection failed. Please try again later.</p>");
}

// Handle Add Entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_entry'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $work_done = $_POST['work_done'];
    $beneficiaries = $_POST['beneficiaries'];
    $Unit=$_SESSION['unit'];

    $stmt = $conn->prepare("INSERT INTO work_done_diary (event_name, event_date, venue, work_done, beneficiaries,Unit) VALUES (?, ?, ?,?, ?, ?)");
    $stmt->bind_param("sssssi", $event_name, $event_date, $venue, $work_done, $beneficiaries,$Unit);
    
    if ($stmt->execute()) {
        echo "<p class='success-msg'>✅ Entry added successfully.</p>";
    } else {
        echo "<p class='error-msg'>❌ Error adding entry.</p>";
    }
}

// Handle Delete Entry
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM work_done_diary WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<p class='error-msg'>❌ Entry deleted successfully.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Done Diary</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
        }

        .main {
            padding: 20px;
        }

        /* Navigation Bar */
        .about_nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            background:#303983(241, 89.00%, 35.70%); /* Keep your original nav color */
        }

        .about_nav ul li {
            padding: 10px 15px;
        }

        .about_nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            display: block;
        }

        .about_nav ul li a.active {
            background:#ffa200;
            border-radius: 5px;
        }

        /* Form Styling */
        .widget {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        form label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        form input, form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        .table-container {
    max-width: 100%;
    max-height: 400px; /* Set a fixed height for vertical scrolling */
    overflow-x: auto;
    overflow-y: auto;
    border: 1px solid #ccc; /* Optional: adds a border around the scrollable area */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Ensures table doesn't shrink too much */
}

/* Table Heading Styling */
th {
    background-color: #007BFF; /* Blue header */
    color: white; /* White text */
    font-weight: bold;
    padding: 12px;
    text-align: left;
    border: 1px solid #0056b3;
    position: sticky;
    top: 0;
    z-index: 2;
}

/* Table Row and Cell Styling */
td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
    white-space: normal; /* Allows text wrapping */
    word-wrap: break-word;
}

/* Alternate Row Colors */
tr:nth-child(even) {
    background-color: #f8f9fa;
}

tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Hover Effect on Rows */
tr:hover {
    background-color: #dbeafe;
    transition: 0.3s;
}

/* Delete Button Styling */
.delete-btn {
    color: red;
    text-decoration: none;
    font-weight: bold;
}

.delete-btn:hover {
    text-decoration: underline;
}

        /* Success & Error Messages */
        .success-msg {
            color: green;
            text-align: center;
            font-weight: bold;
        }

        .error-msg {
            color: red;
            text-align: center;
            font-weight: bold;
        }

        /* Delete Button */
        .delete-btn {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a href="exe_stock.php">Stock</a></li>
                <li><a href="exe_budget.php">Budget/Finance</a></li>
                <li><a href="exe_indent.php">Indent Records</a></li>
                <li><a href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a class="active" href="exe_work_done.php">Work Done Diary</a></li>
            </ul>
        </div>

        <!-- Add Work Done Entry -->
        <section class="widget">
            <h2>Add Work Done Entry</h2>
            <form action="" method="POST">
                <label>Event Name:</label>
                <input type="text" name="event_name" required>
                
                <label>Date:</label>
                <input type="date" name="event_date" required>
                
                <label>Venue:</label>
                <input type="text" name="venue" required>
                
                <label>Work Done:</label>
                <textarea name="work_done" required></textarea>
                
                <label>Beneficiaries:</label>
                <input type="text" name="beneficiaries" required>
                
                <button type="submit" name="add_entry">Add Entry</button>
            </form>

            <!-- Display Work Done Entries -->
            <h2>Work Done Entries</h2>
            <div class="table-container">
    <table border="1">
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Work Done</th>
            <th>Beneficiaries</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM work_done_diary ORDER BY event_date DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['event_name']}</td>
                <td>{$row['event_date']}</td>
                <td>{$row['venue']}</td>
                <td>{$row['work_done']}</td>
                <td>{$row['beneficiaries']}</td>
                <td><a href='?delete={$row['id']}' class='delete-btn'>❌ Delete</a></td>
            </tr>";
        }
        ?>
    </table>
</div>

        </section>
    </div>
</div>

</body>
</html>
