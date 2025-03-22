<?php 
    include "exe_header.php"
?>



<div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
            <ul>
                <li class="dropdown">
                    <a href="exe_int.php?type=housekeeping" class="dropbtn">Housekeeping</a>
                    <div class="dropdown-content">
                        <a href="exe_add_inventory.php?type=housekeeping">Add Inventory</a>
                        <a href="exe_view_inventory.php?type=housekeeping">View Inventory</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="exe_int.php?type=manual"class="dropbtn">Manual</a>
                    <div class="dropdown-content">
                        <a href="exe_add_inventory.php?type=manual">Add Inventory</a>
                        <a href="exe_view_inventory.php?type=manual">View Inventory</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="exe_int.php?type=vessels" class="dropbtn">Vessels</a>
                    <div class="dropdown-content">
                        <a href="exe_add_inventory.php?type=vessels">Add Inventory</a>
                        <a href="exe_view_inventory.php?type=vessels">View Inventory</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="widget">
            <?php
            session_start();
            if (isset($_SESSION['last_seen']) && (time() - $_SESSION['last_seen']) > $_SESSION['timeout']) {
                session_unset();
                session_destroy();
                header("Location: exec_login.php");
                exit();
            }
            $_SESSION['last_seen'] = time();
            if (!isset($_SESSION['exec_id'])) {
                header("Location: exec_login.php");
                exit();
            }

            $exec_id = $_SESSION['exec_id'];
            $conn = new mysqli("localhost", "root", "", "nss_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $type = isset($_GET['type']) ? $_GET['type'] : 'housekeeping';
            $stmt = $conn->prepare("SELECT * FROM inventory WHERE stock_type = ?");
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<h2>Viewing $type Inventory</h2>";
            if ($result->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>Inventory ID</th>
                            <th>Stock Name</th>
                            <th>Unit</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Opening Stock</th>
                            <th>Closing Stock</th>
                            <th>Damaged Stock</th>
                            <th>Replaced Stock</th>
                        </tr>";
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
                            <td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:red; text-align:center;'>No Inventory Records Found</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>

    