<?php
    // Start session
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

    // Database connection
    $conn = new mysqli("localhost", "root", "", "nss_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the inventory item to edit
    if (isset($_GET['id'])) {
        $inventory_id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM inventory WHERE inventory_id = ?");
        $stmt->bind_param("i", $inventory_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = $result->fetch_assoc();
    }

    // Update inventory item in database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stock_name = $_POST['stock_name'];
        $stock_unit = $_POST['stock_unit'];
        $stock_year = $_POST['stock_year'];
        $stock_month = $_POST['month'];
        $opening_stock = $_POST['opening_stock'];
        $closing_stock = $_POST['closing_stock'];
        $damaged_stock_no = $_POST['damaged_stock_no'];
        $replaced_stock_no = $_POST['replaced_stock_no'];

        //vvvvv
        

        // Update inventory details
        $update_stmt = $conn->prepare("UPDATE inventory SET stock_name = ?, stock_unit = ?, stock_year = ?, month = ?, opening_stock = ?, closing_stock = ?, damaged_stock_no = ?, replaced_stock_no = ? WHERE inventory_id = ?");
        $update_stmt->bind_param("ssisiiiii", $stock_name, $stock_unit, $stock_year, $stock_month, $opening_stock, $closing_stock, $damaged_stock_no, $replaced_stock_no, $inventory_id);
        
        if ($update_stmt->execute()) {
            header("Location: exe_int.php"); // Redirect back to the inventory list
        } else {
            echo "<p style='color:red; text-align:center;'>Failed to update inventory item</p>";
        }

        $update_stmt->close();
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Inventory Item</h2>
        <?php if ($inventory): ?>
        <form action="exe_edit_inventory.php?id=<?php echo $inventory['inventory_id']; ?>" method="POST">
            <label for="stock_name">Stock Name:</label>
            <input type="text" id="stock_name" name="stock_name" value="<?php echo $inventory['stock_name']; ?>" required><br><br>

            <label for="stock_unit">Stock Unit:</label>
            <input type="text" id="stock_unit" name="stock_unit" value="<?php echo $inventory['stock_unit']; ?>" required><br><br>

            <label for="stock_year">Stock Year:</label>
            <input type="text" id="stock_year" name="stock_year" value="<?php echo $inventory['stock_year']; ?>" required><br><br>

            <label for="month">Stock month:</label>
            <input type="text" id="month" name="month" value="<?php echo $inventory['month']; ?>" required><br><br>

            <label for="opening_stock">Opening Stock:</label>
            <input type="number" id="opening_stock" name="opening_stock" value="<?php echo $inventory['opening_stock']; ?>" required><br><br>

            <label for="closing_stock">Closing Stock:</label>
            <input type="number" id="closing_stock" name="closing_stock" value="<?php echo $inventory['closing_stock']; ?>" required><br><br>

            <label for="damaged_stock_no">Damaged Stock:</label>
            <input type="number" id="damaged_stock_no" name="damaged_stock_no" value="<?php echo $inventory['damaged_stock_no']; ?>" required><br><br>

            <label for="replaced_stock_no">Replaced Stock:</label>
            <input type="number" id="replaced_stock_no" name="replaced_stock_no" value="<?php echo $inventory['replaced_stock_no']; ?>" required><br><br>

            <button type="submit">Update Inventory</button>
        </form>
        <?php else: ?>
            <p style="color:red;">Inventory item not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
