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

    // Create a connection
    $conn = new mysqli("localhost", "root", "", "nss_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission to add/edit indent record
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sl_no = $_POST['sl_no'];
        $particulars = $_POST['particulars'];
        $month = $_POST['month'];
        $expenses = $_POST['expenses'];
        $status = $_POST['status'];

        if (isset($_POST['record_id']) && !empty($_POST['record_id'])) {
            // Update record
            $record_id = $_POST['record_id'];
            $stmt = $conn->prepare("UPDATE indent_book SET sl_no=?, particulars=?, month=?, expenses=?, status=? WHERE id=?");
            $stmt->bind_param("issdsi", $sl_no, $particulars, $month, $expenses, $status, $record_id);
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO indent_book (sl_no, particulars, month, expenses, status, unit) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdss", $sl_no, $particulars, $month, $expenses, $status, $unit);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: exe_indent.php");
        exit();
    }

    // Delete functionality
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $stmt = $conn->prepare("DELETE FROM indent_book WHERE id=?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: exe_indent.php");
        exit();
    }

   // Fetch records filtered by unit
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = "%$search%";
 // Get the unit from session

$stmt = $conn->prepare("SELECT * FROM indent_book WHERE particulars LIKE ? AND unit = ? ORDER BY sl_no ASC");
$stmt->bind_param("ss", $search_param, $unit);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li><a href="exe_stock.php">Stock</a></li>
                <li><a href="exe_budget.php">Budget/Finance</a></li>
                <li><a class="active" href="exe_indent.php">Indent Records</a></li>
                <li><a href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a href="exe_work_done.php">Work Done Diary</a></li>
            </ul>
        </div>
        
        <div class="widget">
            <h2>Indent Record</h2>
            <div class="form-container">
                <form action="" method="POST" class="styled-form">
                    <h3>Add</h3>
                    <input type="hidden" name="record_id">
                    <label>Sl. No:</label>
                    <input type="number" name="sl_no" required>
                    <label>Particulars:</label>
                    <input type="text" name="particulars" required>
                    <label>Month:</label>
                    <input type="date" name="month" required>
                    <label>Expenses:</label>
                    <input type="number" step="0.01" name="expenses" required>
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="PENDING">PENDING</option>
                        <option value="APPROVED">APPROVED</option>
                        <option value="PO_APPROVED">PO APPROVED</option>
                    </select>
                    <button type="submit" class="btn">Save Record</button>
                </form>
            </div>
            
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Sl. No</th>
                            <th>Particulars</th>
                            <th>Month</th>
                            <th>Expenses</th>
                            
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['sl_no']; ?></td>
                            <td><?php echo $row['particulars']; ?></td>
                            <td><?php echo $row['month']; ?></td>
                            <td><?php echo $row['expenses']; ?></td>
                            
                            <td>
                                <a href="exe_indent.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .form-container { max-width: 500px; margin: auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
    .styled-form label { display: block; margin-top: 10px; font-weight: bold; }
    .styled-form input, .styled-form select { width: 100%; padding: 8px; margin-top: 5px; }
    .btn { display: block; width: 100%; padding: 10px; margin-top: 15px; background: #007bff; color: white; border: none; cursor: pointer; }
    .btn:hover { background: #0056b3; }
    .table-container { margin-top: 20px; }
    .styled-table { width: 100%; border-collapse: collapse; }
    .styled-table th, .styled-table td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    .delete-btn { color: red; text-decoration: none; }
</style>

<script src="script.js"></script>
</body>
</html>
