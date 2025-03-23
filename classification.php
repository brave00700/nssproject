<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <style>
      a.nav2{
        color: var(--primary_1);
        background-color: var(--primary_blue);
        margin-bottom: 2px;
      }
      a.nav2.active {
        color: var(--primary_1) !important;
        background-color: var(--primary_yellow);
      }
      .nav2{
        width: 100% !important;
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

        <!-- Collapsible Section for all links -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <div class="navbar-nav d-flex mx-auto">
            <a class="nav-item nav-link" href="home.php"
              >HOME</a
            >
            <a class="nav-item nav-link active" aria-current="page" href="about.php">ABOUT</a>
            <a class="nav-item nav-link" href="contact.php">CONTACT</a>
            <a class="nav-item nav-link" href="gallery.php">GALLERY</a>
            <a class="nav-item nav-link" href="announcements.php">ANNOUNCEMENTS</a>
            <a class="nav-item nav-link" href="apply.php">APPLY</a>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">LOGIN</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="student/login.php" target="_blank">Student Login</a>
                <a class="dropdown-item" href="exe_portal/exe_login.php" target="_blank">Executive Login</a>
                <a class="dropdown-item" href="po_portal/po_login.php" target="_blank">Programme Officer Login</a>
                <a class="dropdown-item" href="admin_portal/admin_login.php" target="_blank">Admin Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start row">
      <!-- Sub Navbar -->
      <div class="col-12 col-sm-4 col-lg-3">
        <nav class="navbar navbar-light nav2 flex-column">
         <div class="container-fluid">
          <ul class="navbar-nav nav2">
            <li class="nav-item">
              <a class="nav-link nav2" href="mottosymbol.php">MOTTO AND SYMBOL</a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav2" href="objectives.php">OBJECTIVES</a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav2" href="nssvolunteer.php">NSS VOLUNTEER</a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav2 active" href="classification.php">CLASSIFICATION OF PROGRAMMES</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>

    <!-- Content  -->
     <div class="container mt-4 col-12 col-sm-8 col-lg-9">
        <p>
            NSS activities have been divided in to two major groups, namely
            Regular activities and Special camping programme.
          </p>

          <p><strong>Regular Activities</strong></p>

          <p>
            <strong
              >The NSS units organize the regular activities as detailed
              below</strong
            >
          </p>

          <p>
            1. General orientation of NSS Volunteers, NSS Day Celebration – 20
            hrs.<br />
            2. Career guidance for the NSS volunteers – 30 hrs.<br />
            3. Community Work including Campus work and one-day camp – 70 hrs.
          </p>

          <p><strong>Special Camping</strong></p>

          <p>
            Special Camping forms an integral part of National Service Scheme.
            It has special appeal to the youth as it provides unique
            opportunities to the students for group living, collective
            experience sharing and constant interaction with community.
          </p>
    </div>
    </div>

    <!-- Footer and logos -->
    <?php include "footer.php" ?>

    <script src="assets/js/custom.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
