<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
   <style>
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            padding: 10px 15px;
            margin-left: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .search-container button:hover {
            background-color: #0056b3;
        }
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
            <li><a class="active" href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_announcements.php">Manage Announcements</a></li>
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
            <li><a class="active" href="search_applications.php">Search Applications</a></li>
            <li><a href="delete_applications.php">Delete Applications</a></li>
            <li><a href="">####</a></li>
          </ul>
        </div>
        <div class="widget">
        <div class="search-container">
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Enter Register Number" required>
                <button type="submit">Search</button>
            </form>
        </div>
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
                    $sql = "SELECT * FROM applications WHERE register_no LIKE '%$search%'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
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
                        echo "<tr><td colspan='8'>No results found</td></tr>";
                    }

                    $conn->close();
                } else {
                    // If no search input, don't display any rows
                    echo "<tr><td colspan='8'>Please enter a register number to search.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </div>
    </div>
</body>
</html>
