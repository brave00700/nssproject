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
$dbname = "nss_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle carousel image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['carousel_image']) && isset($_POST['carousel_submit'])) {
    $imageName = $_POST['carousel_name'];
    $uploadDir = "../assets/carousel/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = basename($_FILES["carousel_image"]["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["carousel_image"]["tmp_name"], $targetFilePath)) {
            // Insert image file name into database
            $stmt = $conn->prepare("INSERT INTO carousel (name, photo_path) VALUES (?, ?)");
            $photoPath = "./assets/carousel/" . $fileName;
            $stmt->bind_param("ss", $imageName, $photoPath);
            
            if ($stmt->execute()) {
                echo "<script>alert('Carousel image uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading carousel image: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Sorry, there was an error uploading your carousel image.');</script>";
        }
    } else {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed for carousel.');</script>";
    }
}

// Handle gallery image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['gallery_image']) && isset($_POST['gallery_submit'])) {
    $imageName = $_POST['gallery_name'];
    $eventDate = $_POST['event_date'];
    $uploadDir = "../assets/gallery/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = basename($_FILES["gallery_image"]["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $targetFilePath)) {
            // Insert image file name into database
            $stmt = $conn->prepare("INSERT INTO gallery (name, photo_path, event_date) VALUES (?, ?, ?)");
            $photoPath = "./assets/gallery/" . $fileName;
            $stmt->bind_param("sss", $imageName, $photoPath, $eventDate);
            
            if ($stmt->execute()) {
                echo "<script>alert('Gallery image uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading gallery image: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Sorry, there was an error uploading your gallery image.');</script>";
        }
    } else {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed for gallery.');</script>";
    }
}

// Handle carousel image deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['carousel_id_delete'])) {
    $imageId = intval($_POST['carousel_id_delete']);
    
    // First, get the file path
    $stmt = $conn->prepare("SELECT photo_path FROM carousel WHERE id = ?");
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = "../" . $row['photo_path'];
        
        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM carousel WHERE id = ?");
        $deleteStmt->bind_param("i", $imageId);
        
        if ($deleteStmt->execute()) {
            // Try to delete the file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo "<script>alert('Carousel image deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting carousel image: " . $deleteStmt->error . "');</script>";
        }
        $deleteStmt->close();
    } else {
        echo "<script>alert('No carousel image found with the given ID.');</script>";
    }
    $stmt->close();
}

// Handle gallery image deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gallery_id_delete'])) {
    $imageId = intval($_POST['gallery_id_delete']);
    
    // First, get the file path
    $stmt = $conn->prepare("SELECT photo_path FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = "../" . $row['photo_path'];
        
        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
        $deleteStmt->bind_param("i", $imageId);
        
        if ($deleteStmt->execute()) {
            // Try to delete the file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo "<script>alert('Gallery image deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting gallery image: " . $deleteStmt->error . "');</script>";
        }
        $deleteStmt->close();
    } else {
        echo "<script>alert('No gallery image found with the given ID.');</script>";
    }
    $stmt->close();
}

// Fetch carousel images for display
$carouselSql = "SELECT id, name, photo_path FROM carousel";
$carouselResult = $conn->query($carouselSql);

// Fetch gallery images for display
$gallerySql = "SELECT id, name, photo_path, event_date FROM gallery ORDER BY event_date DESC";
$galleryResult = $conn->query($gallerySql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSS Admin Portal - Manage Images</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../adminportal.css">
    <style>
        .flexview {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: flex-start;
            gap: 20px;
            margin: 20px auto;
            padding: 20px;
            width: 90%;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .flexviewcol {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 600px;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: #303983;
            text-align: center;
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #303983;
            padding-bottom: 5px;
        }
        
        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }
        
        .image-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            width: 220px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .image-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .image-box img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        
        .image-box p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #333;
            word-break: break-word;
        }
        
        .image-box form {
            margin-top: 8px;
        }
        
        .image-box button {
            width: 100%;
            padding: 5px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .image-box button:hover {
            background-color: #c82333;
        }
        
        @media (max-width: 768px) {
            .flexview {
                flex-direction: column;
            }
            
            .flexviewcol {
                max-width: 100%;
            }
            
            .image-box {
                width: 100%;
                max-width: 300px;
            }
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
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="manage_applications.php">Manage Applications</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_staff.php">Manage Staff</a></li>
            <li><a href="manage_announcements.php">Announcements</a></li>
            <li><a class="active" href="manage_images.php">Manage Images</a></li>
            <li><a href="manage_more.php">More</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="special_widget">
            <h1 style="text-align: center; color: #303983;">Manage Website Images</h1>
            
            <!-- Upload Forms -->
            <div class="flexview">
                <!-- Carousel Image Upload -->
                <div class="flexviewcol">
                    <div class="upload">
                        <h2>Upload Carousel Image</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <label>Image Name/Caption:</label>
                            <input type="text" name="carousel_name" required>
                            
                            <label>Select Image (JPG, PNG, JPEG, GIF):</label>
                            <input type="file" name="carousel_image" required>
                            
                            <button type="submit" name="carousel_submit">Upload to Carousel</button>
                        </form>
                    </div>
                </div>
                
                <!-- Gallery Image Upload -->
                <div class="flexviewcol">
                    <div class="upload">
                        <h2>Upload Gallery Image</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <label>Image Name/Caption:</label>
                            <input type="text" name="gallery_name" required>
                            
                            <label>Event Date:</label>
                            <input type="date" name="event_date" required>
                            
                            <label>Select Image (JPG, PNG, JPEG, GIF):</label>
                            <input type="file" name="gallery_image" required>
                            
                            <button type="submit" name="gallery_submit">Upload to Gallery</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Carousel Images Display -->
            <h2 class="section-title">Current Carousel Images</h2>
            <div class="image-container">
                <?php 
                if ($carouselResult->num_rows > 0) {
                    while ($row = $carouselResult->fetch_assoc()) { 
                        $imagePath = "../" . $row['photo_path'];
                ?>
                    <div class="image-box">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['name']; ?>">
                        <p><strong>ID:</strong> <?php echo $row['id']; ?></p>
                        <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="carousel_id_delete" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p style='text-align:center;width:100%;'>No carousel images found.</p>";
                }
                ?>
            </div>
            
            <!-- Gallery Images Display -->
            <h2 class="section-title">Current Gallery Images</h2>
            <div class="image-container">
                <?php 
                if ($galleryResult->num_rows > 0) {
                    while ($row = $galleryResult->fetch_assoc()) { 
                        $imagePath = "../" . $row['photo_path'];
                ?>
                    <div class="image-box">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['name']; ?>">
                        <p><strong>ID:</strong> <?php echo $row['id']; ?></p>
                        <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                        <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="gallery_id_delete" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p style='text-align:center;width:100%;'>No gallery images found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>