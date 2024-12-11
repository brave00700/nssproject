<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <style>
    table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<div class="logo-container">
    <img class="sjulogo" src="sjulogo.png" alt="sjulogo" />
    <h1><b style="font-size: 2.9rem;">National Service Scheme</b> <br>
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
            <li><a class="active" href="manage_applications.php">Manage Application</a></li>
            <li><a href="">####</a></li>
            <li><a  href="">###</a></li>
            <li><a href="">####</a></li>
            <li><a href="">####</a></li>
        </ul>
    </div>
    <div class="main">
    <div class="about_main_divide">
        <div class="about_nav">
          <ul>
            <li><a href="show_applications.php">Show All Applications</a></li>
            <li><a href="search_applications.php">Search Applications</a></li>
            <li><a href="delete_applications.php">Delete Applications</a></li>
            <li><a href="">####</a></li>
          </ul>
        </div>
        <div class="widget">
    <table>
        <thead>
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Age</th>
                <th>Blood Group</th>
                <th>Shift</th>
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connect to the database
            $conn = new mysqli("localhost", "root", "", "nss_application");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch data from the 'applications' table
            $sql = "SELECT * FROM applications";
            $result = $conn->query($sql);

            // Check if any data is returned
            if ($result->num_rows > 0) {
                // Loop through the results and display them in table rows
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Register_no']}</td>
                            <td>{$row['Name']}</td>
                            <td>{$row['Phone']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['Age']}</td>
                            <td>{$row['Bloodgroup']}</td>
                            <td>{$row['Shift']}</td>
                            <td>{$row['Gender']}</td>
                          </tr>";
                }
            } else {
                // If no records are found, display a message
                echo "<tr><td colspan='8'>No applications found</td></tr>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</div>
</div>
</body>
</html>
