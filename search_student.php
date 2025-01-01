<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminportal.css">
   <style>
        

    </style>
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Admin Portal</b><br>
        </h1> 
        <img class="nsslogo" src="nss_logo.png" alt="logo" />
</div>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
        <li><a  href="manage_applications.php">Manage Applications</a></li>
            <li><a class="active" href="manage_students.php"> Manage Students</a></li>
            <li><a href="manage_staff.php"> Manage Staff</a></li>
            <li><a href="manage_announcements.php"> Announcements</a></li>
            <li><a href="manage_events.php"> Events</a></li>
            <li><a href="manage_inventory.php">Inventory</a></li>
        </ul>
    </div>
    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
          
            <li><a class="active" href="search_student.php">Search a Student</a></li>
            <li><a href="view_admitted_students.php">View Admitted Students<br> (Unit-wise)</a></li>
            <li><a href="modify_students_details.php">Modify Students Details</a></li>
            <li><a href="change_student_password.php">Change Student Password</a></li>
            
            
          </ul>
        </div>
        <div class="widget">
        <div class="search-container">
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Enter Register Number" required>
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="table-container">

        <table>
            <thead>
                <tr>
                <th>Photo</th>               
                <th>Register Number</th>
                                <th>Unit</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Shift</th>
                                <th>Father's Name</th>
                                <th>Mother's Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Age</th>
                                <th>Address</th>
                                <th>Gender</th>
                                <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display results only if the form is submitted
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && !empty($_POST['search'])) {
                    // Connect to the database
                    $conn = new mysqli("localhost", "root", "", "nss_application");

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Get the search value from POST
                    $search = $conn->real_escape_string($_POST['search']);

                    // Fetch data
                    $sql = "SELECT * FROM admitted_students WHERE register_no LIKE '%$search%'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $photoPath = $row['ProfilePhoto'];
                            echo "<tr>
                                    <td><img src='$photoPath' alt='Profile Photo' style='width: 50px; height: 50px; object-fit: cover; border-radius: 20%;'></td>
                                    <td>{$row['Register_no']}</td>
                                        <td>{$row['Unit']}</td>
                                        <td>{$row['Name']}</td>
                                        <td>{$row['Course']}</td>
                                        <td>{$row['Shift']}</td>
                                        <td>{$row['Father_name']}</td>
                                        <td>{$row['Mother_name']}</td>
                                        <td>{$row['Phone']}</td>
                                        <td>{$row['Email']}</td>
                                        <td>{$row['Age']}</td>
                                        <td>{$row['Address']}</td>
                                        <td>{$row['Gender']}</td>
                                        <td>{$row['Category']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>No results found</td></tr>";
                    }

                    $conn->close();
                } else {
                    // If no search input, don't display any rows
                    echo "<tr><td colspan='12'>Please enter a register number to search.</td></tr>";
                }
                ?>
            </tbody>
        </table>  </div>
    </div>
    </div>
    </div>
</body>
</html>
