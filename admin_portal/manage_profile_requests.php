<?php
require_once __DIR__ . "/../config_db.php";

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Fetch environment variables
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../home.php");
    exit();
}



$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variables
$successMessage = "";
$errorMessage = "";

$studentId = '';
$fieldName = '';

// Create a mapping for field names to display names
$fieldDisplayNames = [
    'name' => 'Name',
    'father_name' => 'Father\'s Name',
    'mother_name' => 'Mother\'s Name',
    'phone' => 'Phone',
    'email' => 'Email',
    'dob' => 'Date of Birth',
    'gender' => 'Gender',
    'address' => 'Address',
    'category' => 'Category',
    'bloodgroup' => 'Blood Group',
    'course' => 'Course',
    'shift' => 'Shift',
    'profile_photo' => 'Profile Photo'
];

// Handle request approval
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_request'])) {
    $requestId = intval($_POST['request_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get request details
        $requestStmt = $conn->prepare("SELECT student_id, field_name, new_value FROM profile_update_requests WHERE request_id = ?");
        $requestStmt->bind_param("i", $requestId);
        $requestStmt->execute();
        $requestResult = $requestStmt->get_result();
        
        if ($requestResult->num_rows > 0) {
            $requestData = $requestResult->fetch_assoc();
            $studentId = $requestData['student_id'];
            $fieldName = $requestData['field_name'];
            $newValue = $requestData['new_value'];


            $newPath = "/assets/uploads/profile_photo/" . $studentId . "." . pathinfo($newValue, PATHINFO_EXTENSION);
            if($fieldName === 'profile_photo'){
                rename(".." . $newValue, ".." . $newPath);
            }
            
            // Update student record
            if($fieldName !== 'profile_photo'){
                $updateStmt = $conn->prepare("UPDATE students SET $fieldName = ? WHERE user_id = ?");
                $updateStmt->bind_param("ss", $newValue, $studentId);
            }else{
                $updateStmt = $conn->prepare("UPDATE students SET $fieldName = ? WHERE user_id = ?");
                $updateStmt->bind_param("ss", $newPath, $studentId);
            }
            $updateStmt->execute();

            // Update request status
            $statusStmt = $conn->prepare("UPDATE profile_update_requests SET status = 'APPROVED' WHERE request_id = ?");
            $statusStmt->bind_param("i", $requestId);
            $statusStmt->execute();

            
            $created_at = date('Y-m-d H:i:s');
            $notice = $fieldDisplayNames[$fieldName] . " update approved.";
            $notifyStmt = $conn->prepare("INSERT INTO student_notifications 
                                     (student_id, notice, created_at) 
                                     VALUES (?, ?, ?)");
            $notifyStmt->bind_param("sss", $studentId, $notice, $created_at);
            if($notifyStmt->execute()){
                // Commit transaction
                $conn->commit();
                $successMessage = "Request approved successfully and student profile updated.";
            }else{
                $errorMessage = "Notification not sent.";
                $conn->rollback();
            }
            
        } else {
            $errorMessage = "Request not found.";
            $conn->rollback();
        }
    } catch (Exception $e) {
        // An error occurred, rollback changes
        $conn->rollback();
        $errorMessage = "Error: " . $e->getMessage();
    }
}


// Handle request rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_request'])) {
    $requestId = intval($_POST['request_id']);
    $rejectReason = isset($_POST['reject_reason']) ? $_POST['reject_reason'] : '';
    
    $studentStmt = $conn->prepare("SELECT student_id, field_name, new_value from profile_update_requests WHERE request_id = ?");
    $studentStmt->bind_param("i", $requestId);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();

    $newValue = '';
    if($studentResult->num_rows > 0){
        $studentData = $studentResult->fetch_assoc();
        $studentId = $studentData['student_id'];
        $newValue = ".." . $studentData['new_value'];
        $fieldName = $fieldDisplayNames[$studentData['field_name']];
    }
    
    // Update request status
    $statusStmt = $conn->prepare("UPDATE profile_update_requests SET status = 'REJECTED' WHERE request_id = ?");
    $statusStmt->bind_param("i", $requestId);

    // Delete the file
    if (file_exists($newValue)) {
        unlink($newValue);
    }
    
    if ($statusStmt->execute()) {
        // Reject message
        $created_at = date('Y-m-d H:i:s');
        $rejectReason = $fieldName . " update rejected: " . $rejectReason;

        $notifyStmt = $conn->prepare("INSERT INTO student_notifications 
                                     (student_id, notice, created_at) 
                                     VALUES (?, ?, ?)");
        $notifyStmt->bind_param("sss", $studentId, $rejectReason, $created_at);
        if($notifyStmt->execute()){
            $successMessage = "Request rejected successfully.";
        }else{
            $errorMessage = "Notification not sent.";
        }
    } else {
        $errorMessage = "Error rejecting request: " . $conn->error;
    }
}

// Fetch all requests for display with student details
$requestsSql = "SELECT pr.request_id, pr.student_id, s.name AS student_name, pr.field_name, 
                pr.old_value, pr.new_value, pr.status, pr.created_at
                FROM profile_update_requests pr
                JOIN students s ON pr.student_id = s.user_id
                ORDER BY 
                    CASE 
                        WHEN pr.status = 'PENDING' THEN 1 
                        WHEN pr.status = 'APPROVED' THEN 2
                        WHEN pr.status = 'REJECTED' THEN 3
                    END,
                    pr.created_at DESC";
$requestsResult = $conn->query($requestsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal - Manage Profile Update Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../adminportal.css">
    <style>
        .special_widget {
            width: 96%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .requests-table th, .requests-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .requests-table th {
            background-color: #303983;
            color: white;
            position: sticky;
            top: 0;
        }
        
        .requests-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .requests-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .search-box {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
        }
        
        .status-filter {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .photo-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .toggle-details {
            background: none;
            border: none;
            color: #303983;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
        }
        
        .request-details {
            display: none;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }
            
            .filters {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box, .status-filter {
                width: 100%;
            }
            
            .requests-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
<header>
  <div class="header-container">
    <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
    <div class="header-content">
      <div class="header-text">NATIONAL SERVICE SCHEME</div>
      <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
      <div class="header-subtext">ADMIN PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>

    <div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_reports.php">Reports & Register</a></li>            
            <li><a href="manage_more.php">More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            
          <li><a  href="manage_students.php">Admitted Students</a></li>
            
            <li><a class="active" href="manage_profile_requests.php">Profile Requests</a></li>
            <li><a  href="view_credit_application.php">Credits Application</a></li>

                <li><a href="change_student_password.php">Change Student Password</a></li>


            
          </ul>
        </div>
        <div class="widget">
            <h1 style="text-align: center; color: #303983;">Manage Student Profile Update Requests</h1>
            
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            
            <!-- Filters Section -->
            <div class="filters">
                <input type="text" id="searchInput" class="search-box" placeholder="Search by Student ID or Name..." onkeyup="filterTable()">
                
                <select id="statusFilter" class="status-filter" onchange="filterTable()">
                    <option value="all">All Requests</option>
                    <option value="PENDING" selected>Pending</option>
                    <option value="APPROVED">Approved</option>
                    <option value="REJECTED">Rejected</option>
                </select>
            </div>
            
            <!-- Requests Table -->
            <div style="overflow-x: auto;">
                <table class="requests-table" id="requestsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Field</th>
                            <th>Current Value</th>
                            <th>Requested Value</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($requestsResult->num_rows > 0) {
                            while ($request = $requestsResult->fetch_assoc()) {
                                // For profile photo, we need special handling
                                $isProfilePhoto = ($request['field_name'] === 'profile_photo');
                                
                                echo "<tr class='request-row' data-status='" . $request['status'] . "' data-student='" . $request['student_id'] . " " . $request['student_name'] . "'>";
                                echo "<td>" . $request['request_id'] . "</td>";
                                echo "<td>" . $request['student_id'] . "<br><small>" . $request['student_name'] . "</small></td>";
                                echo "<td>" . $fieldDisplayNames[$request['field_name']] . "</td>";
                                
                                // Current value display with special handling for profile photo
                                echo "<td>";
                                if ($isProfilePhoto) {
                                    $oldValue = $request['old_value'];
                                    echo $oldValue ? "<img src='.." . $oldValue . "' class='photo-preview' alt='Current'>" : "No photo";
                                } else {
                                    echo htmlspecialchars($request['old_value']);
                                }
                                echo "</td>";
                                
                                // Requested value display with special handling for profile photo
                                echo "<td>";
                                if ($isProfilePhoto) {
                                    $newValue = $request['new_value'];
                                    echo $newValue ? "<img src='.." . $newValue . "' class='photo-preview' alt='New'>" : "No photo";
                                } else {
                                    echo htmlspecialchars($request['new_value']);
                                }
                                echo "</td>";
                                
                                // Status badge
                                echo "<td>";
                                if ($request['status'] === 'PENDING') {
                                    echo "<span class='badge badge-warning'>Pending</span>";
                                } elseif ($request['status'] === 'APPROVED') {
                                    echo "<span class='badge badge-success'>Approved</span>";
                                } elseif ($request['status'] === 'REJECTED') {
                                    echo "<span class='badge badge-danger'>Rejected</span>";
                                }
                                echo "</td>";
                                
                                echo "<td>" . date('d M Y, h:i A', strtotime($request['created_at'])) . "</td>";
                                
                                // Action buttons
                                echo "<td>";
                                if ($request['status'] === 'PENDING') {
                                    echo "<button class='btn btn-success' onclick='approveRequest(" . $request['request_id'] . ")'>Approve</button>";
                                    echo "<button class='btn btn-danger' onclick='showRejectModal(" . $request['request_id'] . ")'>Reject</button>";
                                } else {
                                    echo "<span>Processed</span>";
                                }
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>No requests found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRejectModal()">&times;</span>
            <h2>Reject Request</h2>
            <form id="rejectForm" method="POST">
                <input type="hidden" id="reject_request_id" name="request_id">
                <div class="form-group">
                    <label for="reject_reason">Reason for Rejection (Optional):</label>
                    <textarea name="reject_reason" id="reject_reason" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="reject_request" class="btn btn-danger">Confirm Rejection</button>
            </form>
        </div>
    </div>
    
    <!-- Approve Confirmation form (hidden) -->
    <form id="approveForm" method="POST" style="display:none;">
        <input type="hidden" id="approve_request_id" name="request_id">
        <input type="hidden" name="approve_request" value="1">
    </form>
    
    <script>
        // Function to filter table based on search and status
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#requestsTable tbody tr');
            
            rows.forEach(row => {
                const studentText = row.getAttribute('data-student').toLowerCase();
                const status = row.getAttribute('data-status');
                const matchesSearch = studentText.includes(searchInput);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Function to show reject modal
        function showRejectModal(requestId) {
            document.getElementById('reject_request_id').value = requestId;
            document.getElementById('rejectModal').style.display = 'block';
        }
        
        // Function to close reject modal
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        
        // Function to approve request with confirmation
        function approveRequest(requestId) {
            if (confirm('Are you sure you want to approve this request? This will update the student profile.')) {
                document.getElementById('approve_request_id').value = requestId;
                document.getElementById('approveForm').submit();
            }
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        
        // Set default filter to PENDING on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterTable();
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>