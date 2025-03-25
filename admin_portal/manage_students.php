<?php
require_once __DIR__ . "/../config_db.php";

loadEnv(__DIR__ . '/../.env');
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all students initially
$allStudentsQuery = "SELECT * FROM students";
$allStudentsResult = $conn->query($allStudentsQuery);
$students = [];
while ($row = $allStudentsResult->fetch_assoc()) {
    $students[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admincss/manage_student.css">
    <style>
        .search_form_container { display: flex; gap: 20px; margin-bottom: 20px; }
        .search_form { flex: 1; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .admit-buttons { margin: 5px; padding: 8px 15px; cursor: pointer; }
        img.profile-img { width: 50px; height: 50px; object-fit: cover; border-radius: 20%; }
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
        <li><a class="active" href="manage_students.php">Manage Students</a></li>
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
                <li><a class="active" href="manage_students.php">Admitted Students</a></li>
                <li><a href="admin_approve_attendance.php">Approve Attendance</a></li>
                <li><a href="manage_profile_requests.php">Profile Requests</a></li>
                <li><a href="view_credit_application.php">Credits Application</a></li>
                <li><a href="change_student_password.php">Change Student Password</a></li>
            </ul>
        </div>
        <div class="widget">
            <div class="search_form_container">
                <div class="search_form">
                    <label for="searchInput">Search:</label>
                    <input type="text" id="searchInput" placeholder="Search by any field...">
                </div>
                <div class="search_form">
                    <label for="unitFilter">Filter by Unit:</label>
                    <select id="unitFilter">
                        <option value="">All Units</option>
                        <option value="1">Unit 1</option>
                        <option value="2">Unit 2</option>
                        <option value="3">Unit 3</option>
                        <option value="4">Unit 4</option>
                        <option value="5">Unit 5</option>
                    </select>
                </div>
                <!-- Add this to the search_form_container div, right after the unit filter -->
            <div class="search_form">
                <label for="bloodgroupFilter">Filter by Blood Group:</label>
                <select id="bloodgroupFilter">
                    <option value="">All Blood Groups</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>
                <button id="exportCSV" class="admit-buttons">Export to CSV</button>
            </div>

            <form method="POST" id="studentForm">
                <div class="table-container">
                    <table id="studentsTable">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>ProfilePhoto</th>
                                <th>Register No</th>
                                <th>Unit</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Shift</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Address</th>
                                <th>Category</th>
                                <th>Bloodgroup</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><input type="checkbox" name="register_no[]" value="<?= htmlspecialchars($student['register_no']) ?>"></td>
                                <td>
                                    <?php if (!empty($student['profile_photo'])): ?>
                                        <img src="..<?= htmlspecialchars($student['profile_photo']) ?>" class="profile-img">
                                    <?php else: ?>
                                        No Photo
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($student['register_no']) ?></td>
                                <td><?= htmlspecialchars($student['unit']) ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['course']) ?></td>
                                <td><?= htmlspecialchars($student['shift']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['phone']) ?></td>
                                <td><?= htmlspecialchars($student['father_name']) ?></td>
                                <td><?= htmlspecialchars($student['mother_name']) ?></td>
                                <td><?= htmlspecialchars($student['age']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($student['address']) ?></td>
                                <td><?= htmlspecialchars($student['category']) ?></td>
                                <td><?= htmlspecialchars($student['bloodgroup']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <button type="submit" formaction="modify_std.php" name="modify" class="admit-buttons" onclick="return validateSelection()">Modify</button>
                <button type="submit" formaction="view_report.php" name="view" class="admit-buttons" onclick="return validateSelection()">View Report</button>
                <button type="submit" formaction="change_unit.php" name="change_unit" class="admit-buttons" onclick="return SelectAtLeastOne()">Change Unit</button>
            </form>
        </div>
    </div>
</div>

<script>
// Store original student data
const allStudents = <?= json_encode($students) ?>;

// Filter and display students
function filterStudents() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const unitFilter = document.getElementById('unitFilter').value;
    const bloodgroupFilter = document.getElementById('bloodgroupFilter').value;
    
    const filteredStudents = allStudents.filter(student => {
        const matchesSearch = Object.values(student).some(val => 
            String(val).toLowerCase().includes(searchTerm)
        );
        const matchesUnit = !unitFilter || student.unit == unitFilter;
        const matchesBloodgroup = !bloodgroupFilter || student.bloodgroup === bloodgroupFilter;
        return matchesSearch && matchesUnit && matchesBloodgroup;
    });

    renderStudents(filteredStudents);
}

// Render students to table
function renderStudents(students) {
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '';
    
    students.forEach(student => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td><input type="checkbox" name="register_no[]" value="${student.register_no}"></td>
            <td>${student.profile_photo ? 
                `<img src="../${student.profile_photo}" class="profile-img">` : 
                'No Photo'}</td>
            <td>${student.register_no}</td>
            <td>${student.unit}</td>
            <td>${student.name}</td>
            <td>${student.course}</td>
            <td>${student.shift}</td>
            <td>${student.email}</td>
            <td>${student.phone}</td>
            <td>${student.father_name}</td>
            <td>${student.mother_name}</td>
            <td>${student.age}</td>
            <td>${student.gender}</td>
            <td>${student.address}</td>
            <td>${student.category}</td>
            <td>${student.bloodgroup}</td>
        `;
        
        tbody.appendChild(row);
    });
}

// Export to CSV
function exportToCSV() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const unitFilter = document.getElementById('unitFilter').value;
    const bloodgroupFilter = document.getElementById('bloodgroupFilter').value;
    
    // Filter data same as displayed
    const filteredStudents = allStudents.filter(student => {
        const matchesSearch = Object.values(student).some(val => 
            String(val).toLowerCase().includes(searchTerm)
        );
        const matchesUnit = !unitFilter || student.unit == unitFilter;
        const matchesBloodgroup = !bloodgroupFilter || student.bloodgroup === bloodgroupFilter;
        return matchesSearch && matchesUnit && matchesBloodgroup;
    });

    if (filteredStudents.length === 0) {
        alert("No data to export");
        return;
    }

    // Create CSV content
    const headers = Object.keys(filteredStudents[0]);
    let csvContent = headers.join(",") + "\n";
    
    filteredStudents.forEach(student => {
        const row = headers.map(header => {
            let value = student[header];
            if (header === 'profile_photo') {
                value = value ? window.location.origin + '/' + value : 'No Photo';
            }
            return `"${String(value).replace(/"/g, '""')}"`;
        }).join(",");
        csvContent += row + "\n";
    });

    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `students_${new Date().toISOString().slice(0,10)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterStudents);
document.getElementById('unitFilter').addEventListener('change', filterStudents);
document.getElementById('exportCSV').addEventListener('click', exportToCSV);
// Add this with the other event listeners
document.getElementById('bloodgroupFilter').addEventListener('change', filterStudents);

// Validation functions
function validateSelection() {
    const checkboxes = document.querySelectorAll('#studentForm input[name="register_no[]"]:checked');
    if (checkboxes.length > 1) {
        alert("Please select only one student to modify.");
        return false;
    }
    if (checkboxes.length === 0) {
        alert("Please select at least one student.");
        return false;
    }
    return true;
}

function SelectAtLeastOne() {
    const checkboxes = document.querySelectorAll('#studentForm input[name="register_no[]"]:checked');
    if (checkboxes.length === 0) {
        alert("Please select at least one student.");
        return false;
    }
    return true;
}
</script>
</body>
</html>