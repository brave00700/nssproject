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

include "exe_header.php";

// Database connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Stock categories
$categories = ['Vessels', 'Housekeeping', 'Garden Items', 'Office Inventory'];

// Add stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_stock'])) {
    $category = $_POST['category'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $damaged_stock = $_POST['damaged_stock'];
    $replaced_stock = $_POST['replaced_stock'];
    $purchase_date = $_POST['purchase_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO stock_items (category, item_name, quantity, Unit, damaged_stock, replaced_stock, purchase_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssississ", $category, $item_name, $quantity, $unit, $damaged_stock, $replaced_stock, $purchase_date, $status);
    $stmt->execute();
    echo "<p class='success-msg'>Stock added successfully!</p>";
}

// Delete stock
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM stock_items WHERE id = ? AND Unit = ?");
    $stmt->bind_param("is", $id, $unit);
    $stmt->execute();
    echo "<p class='error-msg'>Stock deleted successfully!</p>";
}

// Category filter
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a class="active" href="exe_stock.php">Stock</a></li>
                <li><a href="exe_budget.php">Budget/Finance</a></li>
                <li><a href="exe_indent.php">Indent Records</a></li>
                <li><a href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a href="exe_work_done.php">Work Done Diary</a></li>
            </ul>
        </div>

        <div class="widget">
            <h2>Manage Stock</h2>
            <form method="POST" class="stock-form">
    <div>
        <label>Category:</label>
        <select name="category" required>
            <?php foreach ($categories as $cat) { echo "<option value='$cat'>$cat</option>"; } ?>
        </select>
    </div>

    <div>
        <label>Item Name:</label>
        <input type="text" name="item_name" required>
    </div>

    <div>
        <label>Quantity:</label>
        <input type="number" name="quantity" required>
    </div>

    <div>
        <label>Damaged Stock:</label>
        <input type="number" name="damaged_stock" value="0" required>
    </div>

    <div>
        <label>Replaced Stock:</label>
        <input type="number" name="replaced_stock" value="0" required>
    </div>

    <div>
        <label>Purchase Date:</label>
        <input type="date" name="purchase_date" required>
    </div>

    <div>
        <label>Stock Status:</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Issued">Issued</option>
            <option value="Low Stock">Low Stock</option>
            <option value="Damaged">Damaged</option>
        </select>
    </div>
    
    <button type="submit" name="add_stock">Add Stock</button>
</form>



            <h3>Filter by Category</h3>
            <form method="GET" class="filter-form">
                <label>Select Category:</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat) {
                        $selected = ($cat == $selected_category) ? "selected" : "";
                        echo "<option value='$cat' $selected>$cat</option>";
                    } ?>
                </select>
            </form>

            <h3>Stock Items</h3>
            <table class="stock-table">
                <tr>
                    <th>Category</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Damaged</th>
                    <th>Replaced</th>
                    <th>Purchase Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php 
                $query = "SELECT * FROM stock_items WHERE Unit = ?";
                if ($selected_category) {
                    $query .= " AND category = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $unit, $selected_category);
                } else {
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $unit);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['category']}</td>
                        <td>{$row['item_name']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['damaged_stock']}</td>
                        <td>{$row['replaced_stock']}</td>
                        <td>{$row['purchase_date']}</td>
                        <td>{$row['status']}</td>
                        <td><a href='?delete={$row['id']}' class='delete-btn'>Delete</a></td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".stock-form");

    form.addEventListener("submit", function (event) {
        let isValid = true;

        // Validate quantity (should be between 1 and 50)
        const quantity = document.querySelector("input[name='quantity']").value;
        if (quantity < 1 || quantity > 50) {
            alert("Quantity should be between 1 and 50.");
            isValid = false;
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
});
</script>

</body>
</html>

<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
}

/* Stock Table */
.stock-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.stock-table th, .stock-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.stock-table th {
    background: #ff6600;
    color: white;
}

.stock-table tr:hover {
    background: #f1f1f1;
}

/* Buttons & Forms */
.delete-btn {
    color: red;
    text-decoration: none;
    font-weight: bold;
}

.delete-btn:hover {
    text-decoration: underline;
}

.stock-form, .filter-form {
    margin-bottom: 20px;
}

.success-msg {
    color: green;
    font-weight: bold;
}

.error-msg {
    color: red;
    font-weight: bold;
}
/* Widget Container */
.widget {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    max-width: 800px; /* Adjusted width */
    overflow-x: auto; /* Enable horizontal scrolling on smaller screens */
}

/* Form Container */
.stock-form, .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
}

/* Arrange Fields in Two Columns */
.stock-form div, .filter-form div {
    display: flex;
    flex: 1 1 48%; /* Two equal columns */
    flex-direction: column;
    min-width: 280px; /* Prevent items from getting too small */
}

/* Inputs & Select */
.stock-form input, .stock-form select,
.filter-form select {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    width: 100%;
}

/* Submit Button */
.stock-form button {
    background: #ff6600;
    color: white;
    font-size: 16px;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    width: 100%;
}

.stock-form button:hover {
    background: #e55d00;
}

/* Responsive: Enable Horizontal Scrolling */
@media (max-width: 600px) {
    .widget {
        max-width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }

    .stock-form, .filter-form {
        display: block; /* Stack fields in one column for small screens */
    }
}

</style>
