<?php
$conn = new mysqli("sql12.freesqldatabase.com", "sql12767434", "fdUtRRwmJ9", "sql12767434");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$stmt = $conn->prepare("SELECT id, name, file FROM announcements LIMIT 5");
$stmt->execute();
$result_student = $stmt->get_result();

$stmt1 = $conn->prepare("SELECT photo_path FROM gallery LIMIT 3");
$stmt1->execute();
$result_2 = $stmt1->get_result();

$stmt2 = $conn->prepare("SELECT photo_path FROM carousel");
$stmt2->execute();
$result_3 = $stmt2->get_result();

// Generating the carousel
$count = 1;
      $indicators = "";
      $sliders = "";
      while($row3 = $result_3->fetch_assoc()){
        $cminus = $count - 1;
        if($count == 1){
          $indicators .= "<button
          type='button'
          data-bs-target='#robotcarousel'
          data-bs-slide-to='{$cminus}'
          class='active'
          aria-current='true'
          aria-label='Slide {$count}'
        ></button>";
          
          $sliders .= "<div class='carousel-item active'>
          <img
            class='d-block w-100'
            src='{$row3["photo_path"]}'
            alt='Image {$count}'
          />
        </div>";
        }else{
          $indicators .= "<button
          type='button'
          data-bs-target='#robotcarousel'
          data-bs-slide-to='{$cminus}'
          aria-label='Slide {$count}'
            ></button>";

        $sliders .= "<div class='carousel-item'>
        <img
          class='d-block w-100'
          src='{$row3["photo_path"]}'
          alt='Image {$count}'
            />
          </div>";
        }
        $count++;
      }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <style>
      .carousel-item img {
        object-fit: cover; 
        height: 100vh;     
      }

      .carousel {
        height: 25rem;
        overflow: hidden;
        display: flex;
        align-items: center;
      }

      .carousel-control-prev-icon,
      .carousel-control-next-icon {
        background-color: var(--primary_blue);
        width: 40px;
        height: 40px;
        border-radius: 50%;
      }
    </style>
  
  </head>
  <body>
    <!-- Header -->
    <?php include "header.php" ?>

    <!-- Navbar -->
    <nav
      class="navbar navbar-expand-sm navbar-dark p-0"
      style="background-color: var(--primary_blue)"
    >
      <div class="container-fluid d-flex justify-content-between">
        <!-- Toggler for collapsing links -->
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarCollapse"
          aria-controls="navbarCollapse"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Section for all links except LOGIN -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <div class="navbar-nav d-flex mx-auto">
            <a class="nav-item nav-link active" aria-current="page" href="home.php"
              >HOME</a
            >
            <a class="nav-item nav-link" href="about.php">ABOUT</a>
            <a class="nav-item nav-link" href="contact.php">CONTACT</a>
            <a class="nav-item nav-link" href="gallery.php">GALLERY</a>
            <a class="nav-item nav-link" href="announcements.php">ANNOUNCEMENTS</a>
            <a class="nav-item nav-link" href="apply.php">APPLY</a>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LOGIN</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="student/login.php" target="_blank">Student Login</a>
                <a class="dropdown-item" href="exe_portal/exe_login.php" target="_blank">Executive Login</a>
                <a class="dropdown-item" href="po_portal/po_login.php" target="_blank">Program Officer Login</a>
                <a class="dropdown-item" href="admin_portal/admin_login.php" target="_blank">Admin Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Carousel  -->
    <div
      id="robotcarousel"
      class="carousel slide"
      data-bs-interval="5000"
      data-pause="true"
      data-bs-ride="carousel"
    >
      <div class="carousel-indicators">
        <?php echo $indicators; ?>
      </div>

      <div class="carousel-inner">
        <?php echo $sliders;   ?>
      </div>

      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#robotcarousel"
        data-bs-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#robotcarousel"
        data-bs-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

    <!-- Content -->

    <div class="container">
      The National Service Scheme (NSS) is a Central Sector Scheme of Government
      of India, Ministry of Youth Affairs & Sports. It provides opportunity to
      the student youth of 11th & 12th Class of schools at +2 Board level and
      student youth of Technical Institution, Graduate & Post Graduate at
      colleges and University level of India to take part in various
      government-led community service activities & programmes. The sole aim of
      the NSS is to provide hands-on experience to young students in delivering
      community service.
    </div>

    <!-- Announcements and gallery -->

    <div class="container mt-4">
      <div class="row">
          <!-- Announcements Card -->
          <div class="col-md-6 mb-2" >
              <div class="card" style="height: 250px;">
                  <div class="card-header text-white" style="background-color: var(--primary_blue)">Announcements</div>
                  <div class="card-body">
                      <ul>
                        <?php
                          while($row = $result_student->fetch_assoc()){
                            echo "<li>{$row["name"]}</li>";
                          }
                        ?>
                      </ul>
                  </div>
              </div>
          </div>
  
          <!-- Gallery Card -->
          <div class="col-md-6 mb-2">
              <div class="card" style="height: 250px;">
                  <div class="card-header text-white" style="background-color: var(--primary_blue)">Gallery</div>
                  <div class="card-body">
                      <div class="row" style="height: 100%;">
                        <?php
                          while($row2 = $result_2->fetch_assoc()){
                            echo "<div class='col-4' ><img src='{$row2["photo_path"]}' class='img-fluid rounded' style='object-fit: cover; height: 100%;' alt='Image 1'></div>";
                          }
                        ?>
                      </div>
                  </div>
              </div>
          </div>
     </div>
     </div>

    <!-- Footer and logos -->
    <?php include "footer.php" ?>

    <script src="assets/js/custom.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
