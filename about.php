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
        color: var(--primary_blue) !important;
        font-weight: bolder;
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

        <!-- Collapsible Section for all links except LOGIN -->
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
              <a class="nav-link nav2" href="classification.php">CLASSIFICATION OF PROGRAMMES</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>

  <!-- Content  -->
   <div class="container mt-4 col-12 col-sm-8 col-lg-9">
      <p>
          <strong>NATIONAL SERVICE SCHEME (N.S.S)</strong>
        </p>

        <p>
          The National Service Scheme (NSS) is an Indian government-sponsored
          public service program conducted by the Ministry of Youth Affairs
          and Sports of the Government of India. Popularly known as NSS, the
          scheme was launched in Gandhiji's Centenary year in 1969.&nbsp;
        </p>

        <p>
          St Joseph's University has been continuously in NSS since its
          inception with the major aim of providing opportunities to the
          students to involve themselves in social activities and ultimately
          developing their personality. It is also known to have one of the
          oldest NSS units in Karnataka State, has completed 50 years,
          reflecting the essence of democratic living and upholding the need
          for selfless service empowering youth and promote national
          integration.
        </p>

        <p>
          Service of faith and promotion of justice is a significant component
          of Jesuit education across the globe. It is imperative that our
          education need to make our students have a deep and profound faith
          in themselves, in each other and the divine, which needs to be
          realized in the promotion of justice for all, especially the last,
          the least, and the lost who are on the periphery of society.
          Ultimately we need to prepare students who serve the nation with
          resolute devotion and single-minded commitment.
        </p>

        <p>
          National Service Scheme is a voluntary organization of young
          students that provides services to society without bias. It aims to
          doctrine social welfare in students through community services.
          Students are the building blocks of society. It is a proven fact
          that organized group activities are potential enabling factors for
          cognitive developments and enriching the spirit of brotherhood. We
          at NSS, strive to achieve similar goals through activities which
          have a direct impact on the society and environment at large
        </p>

        <p>
          <strong>Year of NSS established</strong> : 24 September 1969<br />
          <strong>Advisory committee chairman and Vice-chancellor</strong>:
          Dr. Fr. Victor Lobo S.J,<br />
          <strong>NSS Program Coordinator</strong>:
          <a
            href="https://sju.edu.in/faculty-details/st-joseph-university/computer-science-and-computer-application/NDYy"
            target="_blank"
            >Mr. Selwyn Paul. J</a
          ><br />
          <strong>No. of NSS units in college</strong>: 5<br/>
          <strong>Email Id </strong>:
          <a href="mailto:nss@sju.edu.in">nss@sju.edu.in</a>
        </p>
  </div>
    </div>

    <!-- Footer and logos -->
    <?php include "footer.php" ?>

    <script src="assets/js/custom.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
