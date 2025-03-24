<?php
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
    header("Location: exe_login.php");
    exit();
}
$_SESSION['last_seen'] = time();

// Check if executive is logged in
if (!isset($_SESSION['exec_id'])) {
    header("Location: exec_login.php");
    exit();
}

$exec_id = $_SESSION['exec_id'];

// Create a connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
    <h1>  
        <b style="font-size: 2.9rem;">National Service Scheme</b> <br>
        <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Executive Portal</b><br>
        </h1>
    <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
    <div class="ham-menu">
        <a><i class="fa-solid fa-bars ham-icon"></i></a>
    </div>
    
    <ul>
        <li><a href="exe_profile.php">Profile</a></li>
        <li><a class="active" href="exe_int.php">Inventory</a></li>
        <li><a href=".php">###</a></li>
        <li><a href=".php">###</a></li>
        <li><a href=".php">####</a></li>
        <li><a href="exe_logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a  href="exe_int.php">View Inventory</a></li>
                <li><a class="active" href="exe_cr_in.php">create and edit</a></li>
            </ul>
        </div>
        <div class="widget">

            <!-- Form to add new inventory entry -->
            <h2>Add New Inventory Entry</h2>
            <form method="POST" >
                <label for="stock_name">Stock Name:</label>
                <input type="text" name="stock_name" id="stock_name" required><br>

                <label for="stock_unit">Stock Unit:</label>
                <select name="stock_unit" id="stock_unit" required>
                    <option value="1">Unit 1</option>
                    <option value="2">Unit 2</option>
                    <option value="3">Unit 3</option>
                    <option value="4">Unit 4</option>
                    <option value="5">Unit 5</option>
                </select><br>

                <label for="stock_year">Stock Year:</label>
                <input type="number" name="stock_year" id="stock_year" required><br>
                <label for="stock_month">Stock Month:</label>
                <input type="month" name="month" id="month" required><br>

                <label for="opening_stock">Opening Stock:</label>
                <input type="number" name="opening_stock" id="opening_stock" required><br>

                <label for="closing_stock">Closing Stock:</label>
                <input type="number" name="closing_stock" id="closing_stock" required><br>

                <label for="damaged_stock_no">Damaged Stock No:</label>
                <input type="number" name="damaged_stock_no" id="damaged_stock_no" required><br>

                <label for="replaced_stock_no">Replaced Stock No:</label>
                <input type="number" name="replaced_stock_no" id="replaced_stock_no" required><br>

                <button type="submit">Add Inventory</button>
            </form>
        </div>
    </div>
</div>
<?php
// Insert inventory entry when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecting form data
    $stock_name = $_POST['stock_name'];
    $stock_unit = $_POST['stock_unit'];
    $stock_year = $_POST['stock_year'];
    $stock_month = $_POST['month'];
    $opening_stock = $_POST['opening_stock'];
    $closing_stock = $_POST['closing_stock'];
    $damaged_stock_no = $_POST['damaged_stock_no'];
    $replaced_stock_no = $_POST['replaced_stock_no'];

    // Prepare the SQL insert query
    $stmt = $conn->prepare("INSERT INTO inventory (stock_name, stock_unit, stock_year,month, opening_stock, closing_stock, damaged_stock_no, replaced_stock_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiii", $stock_name, $stock_unit, $stock_year,$stock_month, $opening_stock, $closing_stock, $damaged_stock_no, $replaced_stock_no);

    // Execute query
    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Inventory entry added successfully.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error adding inventory entry: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Query to retrieve inventory data
$stmt = $conn->prepare("SELECT inventory_id, stock_name, stock_unit, stock_year, month,opening_stock, closing_stock, damaged_stock_no, replaced_stock_no FROM inventory");
$stmt->execute();
$result = $stmt->get_result();

// Check if inventory data is available
if ($result->num_rows > 0) {
    echo "<table border='2'>
            <tr>
                <th>Inventory ID</th>
                <th>Stock Name</th>
                <th>Unit</th>
                <th>Year</th>
                <th>month</th>
                <th>Opening Stock</th>
                <th>Closing Stock</th>
                <th>Damaged Stock</th>
                <th>Replaced Stock</th>
                 <th>Action</th> <!-- New Column for Edit Button -->
            </tr>";

    // Loop through the inventory results
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['inventory_id']}</td>
                <td>{$row['stock_name']}</td>
                <td>{$row['stock_unit']}</td>
                <td>{$row['stock_year']}</td>
                <td>{$row['month']}</td>
                <td>{$row['opening_stock']}</td>
                <td>{$row['closing_stock']}</td>
                <td>{$row['damaged_stock_no']}</td>
                <td>{$row['replaced_stock_no']}</td>
                <td><a href='exe_edit_inventory.php?id={$row['inventory_id']}'>Edit</a></td> <!-- Edit link -->
            </tr>";
    }

    echo "</table>";
} else {
    echo "<p style='color:red; text-align:center;'>No Inventory Records Found</p>";
}

$stmt->close();
$conn->close();
?>
<script src="script.js"></script>
</body>
</html>
