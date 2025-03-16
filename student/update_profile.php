<?php
require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();

// Create a connection object
$conn = getDatabaseConnection();

// Initialize variables
$successMessage = "";
$errorMessage = "";
$updateableFields = [
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
$categoryList = [
    'GENERAL', 'OBC', 'SC', 'ST'
];
$bloodgroupList = [
    'B+', 'B-', 'A+', 'A-', 'AB+', 'AB-', 'O-', 'O+'
];
$genderList = [
    'MALE', 'FEMALE', 'OTHER'
];
$shiftList = [
    '1', '2', '3'
];

$minDate = (new DateTime())->modify('-50 years'); // Assuming max age of 50 years
$maxDate = new DateTime();
$maxDate->modify('-16 years')->setDate($maxDate->format('Y'), 12, 31); // Assuming min age of 16 years

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("s", $reg);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 0) {
    echo "User Not Found";
    header("Location: login.php");
    exit();
}

$userData = $result->fetch_assoc();

// Check for pending requests
$pendingRequestsStmt = $conn->prepare("SELECT field_name FROM profile_update_requests 
                                      WHERE student_id = ? AND status = 'PENDING'");
$pendingRequestsStmt->bind_param("s", $reg);
$pendingRequestsStmt->execute();
$pendingResult = $pendingRequestsStmt->get_result();
$pendingFields = [];

while ($row = $pendingResult->fetch_assoc()) {
    $pendingFields[] = $row['field_name'];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {

    $field = $_POST['field_name'];
    $oldValue = $userData[$field];
    $newValue = '';

    if($field !== 'profile_photo'){
        $newValue = trim($_POST['new_value']);
    }
    
    // Validate input based on field type
    $isValid = true;
    
    if (empty($newValue) && $field !== 'profile_photo') {
        $errorMessage = "New value cannot be empty.";
        $isValid = false;
    } else if ($newValue === $oldValue) {
        $errorMessage = "New value is the same as current value.";
        $isValid = false;
    } else if (($field == 'name' || $field == 'mother_name' || $field == 'father_name') && !preg_match('/^[A-Za-z ]{0,50}$/', $newValue)) {
        $errorMessage = "$updateableFields[$field] should contain only alphabets from A-Z or a-z.";
        $isValid = false;
    } else if ($field == 'phone' && !preg_match('/^[0-9]{10}$/', $newValue)) {
        $errorMessage = "Please enter a valid 10-digit phone number.";
        $isValid = false;
    } else if ($field == 'email' && !filter_var($newValue, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
        $isValid = false;
    } else if ($field == 'dob') {
        // Validate date format and reasonable age
        $dobDate = DateTime::createFromFormat('Y-m-d', $newValue);
        $now = new DateTime();
        
        
        if (!$dobDate || $dobDate->format('Y-m-d') !== $newValue) {
            $errorMessage = "Please enter a valid date in DD-MM-YYYY format.";
            $isValid = false;
        } else if ($dobDate > $maxDate || $dobDate < $minDate) {
            $errorMessage = "Please enter a reasonable date of birth (between 16 and 50 years old).";
            $isValid = false;
        }
    } else if ($field == 'category' && !in_array($newValue, $categoryList)) {
        $errorMessage = "Invalid Category. Allowed values: " . implode(", ",$categoryList);
        $isValid = false;
    } else if ($field == 'bloodgroup' && !in_array($newValue, $bloodgroupList)) {
        $errorMessage = "Invalid Bloodgroup. Allowed values: " . implode(", ",$bloodgroupList);
        $isValid = false;
    } else if ($field == 'gender' && !in_array($newValue, $genderList)) {
        $errorMessage = "Invalid Gender. Allowed values: " . implode(", ",$genderList);
        $isValid = false;
    } else if ($field == 'shift' && !in_array($newValue, $shiftList)) {
        $errorMessage = "Invalid Shift. Allowed values: " . implode(", ",$shiftList);
        $isValid = false;
    } else if ($field == 'course' && !preg_match('/^[A-Za-z0-9 ]{0,50}$/', $newValue)) {
        $errorMessage = "$updateableFields[$field] should contain only alphanumeric characters such as A-Z, a-z, 0-9.";
        $isValid = false;
    } else if ($field == 'profile_photo'){
        // Handle file upload
        if(isset($_FILES['new_value']) && $_FILES['new_value']['error'] === UPLOAD_ERR_OK){
            $fileTmpPath = $_FILES['new_value']['tmp_name'];
            $fileName = $_FILES['new_value']['name'];
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileSize = $_FILES['new_value']['size'];
            $fileType = mime_content_type($fileTmpPath);
            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxFileSize = 2 * 1024 * 1024; //2MB

            // Validate file size
            if($fileSize > $maxFileSize) {
                $errorMessage = 'Error: File size exceeds 2MB limit.';
                exit();
            }

            // Validate file type
            if(!in_array($fileType, $allowedFileTypes)) {
                $errorMessage = 'Error: Invalid file type. Only JPEG, PNG and JPG are allowed.';
            }

            // Define the upload directory
            $uploadDir = "../assets/uploads/student_req/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create directory if not exists
            }

            // Define the path where the photo will be saved
            $filePath = $uploadDir . $reg . date("dmis") . "." . $fileExt;


            // Move the uploaded file to the specified directory
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $newValue = "/assets/uploads/student_req/" . $reg . date("dmis") . "." . $fileExt;
            } else {
                $errorMessage =  "Error: Failed to upload the profile photo. Please try again.";
                exit();
            }
        }
    }
    
    // Check if there's already a pending request for this field
    if (in_array($field, $pendingFields)) {
        $errorMessage = "You already have a pending request for this field.";
        $isValid = false;
    }
    
    if ($isValid) {
        // Insert update request
        $insertStmt = $conn->prepare("INSERT INTO profile_update_requests 
                                     (student_id, field_name, old_value, new_value) 
                                     VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("ssss", $reg, $field, $oldValue, $newValue);
        
        if ($insertStmt->execute()) {
            $successMessage = "Update request submitted successfully.";
            // Refresh pending fields
            $pendingRequestsStmt->execute();
            $pendingResult = $pendingRequestsStmt->get_result();
            $pendingFields = [];
            while ($row = $pendingResult->fetch_assoc()) {
                $pendingFields[] = $row['field_name'];
            }
        } else {
            $errorMessage = "Error submitting request: " . $conn->error;
        }
    }
}

// Fetch all pending requests for display
$allRequestsStmt = $conn->prepare("SELECT * FROM profile_update_requests 
                                  WHERE student_id = ? 
                                  ORDER BY created_at DESC");
$allRequestsStmt->bind_param("s", $reg);
$allRequestsStmt->execute();
$allRequests = $allRequestsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .widget {
            width: 100%;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
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
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
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
            padding: 8px;
            text-align: left;
        }
        
        .requests-table th {
            background-color: #f2f2f2;
        }
        
        .requests-table tr:nth-child(even) {
            background-color: #f9f9f9;
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

        h2 {
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        
        /* Specific styles for date input */
        input[type="date"] {
            padding: 7px;
        }
        
        /* Helper text */
        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include "header.php" ?>
   
    <div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a class="active" href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="grievance.php">Grievience</a></li>
            <li><a href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="about_main_divide">
            <div class="about_nav">
                <ul>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a class="active" href="update_profile.php">Request Profile Update</a></li>
                    <li><a href="pass_change.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
            <div class="widget">
                <h2>Request Profile Information Update</h2>
                
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
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="field_name">Field to Update:</label>
                        <select name="field_name" id="field_name" class="form-control" required>
                            <option value="">Select field</option>
                            <?php foreach ($updateableFields as $field => $label): ?>
                                <?php $disabled = in_array($field, $pendingFields) ? 'disabled' : ''; ?>
                                <option value="<?php echo $field; ?>" <?php echo $disabled; ?>>
                                    <?php echo $label; ?> 
                                    <?php if (in_array($field, $pendingFields)): ?>
                                        (Pending)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_value">Current Value:</label>
                        <input type="text" id="current_value" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_value">New Value:</label>
                        <input type="text" name="new_value" id="new_value" class="form-control" required>
                        <div id="field_help" class="help-text"></div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="submit_request" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
                
                <h2>My Update Requests</h2>
                <?php if ($allRequests->num_rows > 0): ?>
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Current Value</th>
                                <th>Requested Value</th>
                                <th>Status</th>
                                <th>Requested On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($request = $allRequests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $updateableFields[$request['field_name']] ?? $request['field_name']; ?></td>
                                    <td><?php echo htmlspecialchars($request['old_value']); ?></td>
                                    <td><?php echo htmlspecialchars($request['new_value']); ?></td>
                                    <td>
                                        <?php if ($request['status'] == 'PENDING'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif ($request['status'] == 'APPROVED'): ?>
                                            <span class="badge badge-success">Approved</span>
                                        <?php elseif ($request['status'] == 'REJECTED'): ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($request['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No update requests found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fieldSelect = document.getElementById('field_name');
        const currentValueInput = document.getElementById('current_value');
        const newValueInput = document.getElementById('new_value');
        const fieldHelp = document.getElementById('field_help');
        const userData = <?php echo json_encode($userData); ?>;
        const minDate = "<?php echo $minDate->format('Y-m-d'); ?>";
        const maxDate = "<?php echo $maxDate->format('Y-m-d'); ?>";

        
        fieldSelect.addEventListener('change', function() {
            const selectedField = this.value;
            if (selectedField && userData[selectedField]) {
                currentValueInput.value = userData[selectedField];
                
                // Change input type based on field
                
                if (selectedField === 'profile_photo') {
                    currentValueInput.value = '';
                    newValueInput.type = 'file';
                }
                else if (selectedField === 'dob') {
                    newValueInput.type = 'date';
                    newValueInput.setAttribute("max", maxDate);
                    newValueInput.setAttribute("min", minDate);
                    fieldHelp.textContent = 'Enter date in DD-MM-YYYY format';
                } else {
                    if(newValueInput.hasAttribute('min') && newValueInput.hasAttribute('max')){
                        newValueInput.removeAttribute('min');
                        newValueInput.removeAttribute('max');
                    }
                    if (selectedField === 'phone') {
                        newValueInput.type = 'tel';
                        fieldHelp.textContent = 'Enter a 10-digit phone number';
                    } else if (selectedField === 'email') {
                        newValueInput.type = 'email';
                        fieldHelp.textContent = 'Enter a valid email address';
                    } else if (selectedField === 'name' || selectedField === 'mother_name' || selectedField === 'father_name') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = `${selectedField.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')} should contain only alphabets from A-Z or a-z`;
                    } else if (selectedField === 'category') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Allowed values: <?php echo implode(", ", $categoryList) ?>';
                    } else if (selectedField === 'bloodgroup') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Allowed values: <?php echo implode(", ", $bloodgroupList) ?>';
                    } else if (selectedField === 'address') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Enter a valid address';
                    } else if (selectedField === 'gender') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Allowed values: <?php echo implode(", ", $genderList) ?>';
                    } else if (selectedField === 'shift') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Allowed values: <?php echo implode(", ", $shiftList) ?>';
                    } else if (selectedField === 'course') {
                        newValueInput.type = 'text';
                        fieldHelp.textContent = 'Include the year and section of your course. Eg. 2 BCOM D';
                    }
                }
                
                // Clear the new value input
                newValueInput.value = '';
            } else {
                currentValueInput.value = '';
                newValueInput.type = 'text';
                fieldHelp.textContent = '';
            }
        });
    });
    </script>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>