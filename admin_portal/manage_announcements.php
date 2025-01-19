<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pdf_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    $fileName = $_FILES['pdf_file']['name'];
    $fileTmpName = $_FILES['pdf_file']['tmp_name'];
    $fileType = $_FILES['pdf_file']['type'];

    if ($fileType == "application/pdf") {
        $fileData = file_get_contents($fileTmpName);

        $stmt = $conn->prepare("INSERT INTO pdf_files (name, file) VALUES (?, ?)");
        $stmt->bind_param("sb", $fileName, $null);

        $stmt->send_long_data(1, $fileData);

        if ($stmt->execute()) {
            echo "<script>alert('Announcement pdf uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Error: ');</script>" . $stmt->error;
        }

        $stmt->close();
    } else {
        echo " <script>alert('Please upload a PDF file.')</script>";
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pdf_id'])) {
    $pdfId = intval($_POST['pdf_id']);

    // Check if the PDF exists in the database
    $stmt = $conn->prepare("SELECT id FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $pdfId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // PDF exists, proceed to delete
        $deleteStmt = $conn->prepare("DELETE FROM pdf_files WHERE id = ?");
        $deleteStmt->bind_param("i", $pdfId);

        if ($deleteStmt->execute()) {
            echo "<script>alert('PDF file deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting the PDF file.');</script>";
        }

        $deleteStmt->close();
    } else {
        echo "<script>alert('No PDF found with the given ID.');</script>";
    }

    $stmt->close();
}


// Fetch PDFs for viewing
$sql = "SELECT id, name FROM pdf_files";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../adminportal.css">
    <style>
        .flexview {
    display: flex;
    
    justify-content: space-around;
    align-items: flex-start;
    gap: 20px; /* Space between the forms */
    margin: 20px auto;
    padding: 20px;
    width: 90%; /* Adjust width as needed */
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

flexviewcol{
    display: flex;
    flex-direction:column;
}






    </style>
</head>
<body>
<div class="logo-container">
        <img class="sjulogo" src="../sjulogo.png" alt="sjulogo" />
        <h1>  <b style="font-size: 2.9rem;">National Service Scheme </b> <br>
            <div style="font-size: 1.5rem;color: black;">St Joseph's University, Bengaluru. <br>
            <b style="font-size: 1.3rem">Admin Portal</b><br>
        </h1> 
        <img class="nsslogo" src="../nss_logo.png" alt="logo" />
</div>

<div class="nav">
    <ul>
        <li><a href="manage_applications.php">Manage Applications</a></li>
        <li><a href="view_admitted_students.php">Manage Students</a></li>
        <li><a href="view_po.php">Manage Staff</a></li>
        <li><a class="active" href="manage_announcements.php">Announcements</a></li>
        <li><a href="manage_events.php">Events</a></li>
        <li><a href="admin_logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
<div class="special_widget">
       
            <div class="flexview">
            <div class="flexviewcol">
            <div class="upload">
                <h2>Upload a PDF File</h2>
                <?php if (isset($uploadMessage)) ?>
                <form method="POST" enctype="multipart/form-data">
                    <label>Select PDF File:</label>
                    <input type="file" name="pdf_file" required>
                    <button type="submit">Upload</button>
                </form>
            </div>
            
            <div class="delete">
                <h2>Delete a PDF Announcement</h2>
                <?php if (isset($deleteMessage)) ?>
                <form method="POST">
                    <label for="pdf_id">Enter PDF ID:</label>
                    <input type="number" name="pdf_id" id="pdf_id" required>
                    <button type="submit">Delete</button>
                </form>
            </div>
</div>
            <div class="announcements">
                <h1>Announcements</h1>
                <div class="box"> <ul>
                <ul>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <li><a href="download.php?id=<?= $row['id'] ?>" target="_blank"><?= $row['id'] ?>: <?= $row['name'] ?></a></li>
                    <?php } ?>
                </ul>
                    </div>
            </div>

            </div>
        
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
