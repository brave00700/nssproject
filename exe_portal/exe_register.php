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

            ?>
<?php 
    include "exe_header.php"
?>

<div class="main">
    <div class="about_main_divide">
        <!-- Left navigation with Stock, Budget and Report buttons -->
        <div class="about_nav">
            <ul>
                <li><a  href="exe_stock.php">Stock</a></li>
                <li><a href="exe_budget.php">Budget/Finance</a></li>
                <li><a href="exe_indent.php">Indent Records</a></li>
                <li><a href="exe_mom.php">Minutes of Meeting</a></li>
                <li><a href="exe_work_done.php">Work Done Diary</a></li>

            </ul>
        </div>
        
        <!-- Main content area -->
        <div class="widget">
            
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>