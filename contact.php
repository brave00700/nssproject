<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
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
            <a class="nav-item nav-link" href="about.php">ABOUT</a>
            <a class="nav-item nav-link active" aria-current="page" href="contact.php">CONTACT</a>
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

    <!-- Content  -->
     <div class="container mt-4">
    <p style="text-align: justify">
        <strong>NSS Program Coordinator</strong>:
        <a
          href="https://sju.edu.in/faculty-details/st-joseph-university/computer-science-and-computer-application/NDYy"
          target="_blank"
          >Mr. Selwyn Paul. J</a
        ><br />
        <strong>No. of NSS units in college</strong>: 5
      </p>

      <p style="text-align: justify">
        <strong>Email Id </strong>:
        <a href="mailto:nss@sju.edu.in">nss@sju.edu.in</a>
      </p>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">Sl.No.</th>
            <th scope="col" >NSS Unit</th>
            <th scope="col" >
              NSS Programme Officer
            </th>
            <th scope="col" >
              Assistant NSS Programme Officer
            </th>
            <th scope="col" >Shift</th>
            <th scope="col">University Code</th>
            <th scope="col">Email Id</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" >1</th>
            <td >SJU NSS UNIT 1</td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/department-of-management/MTQwMg=="
                target="_blank"
                >Mr Samuel Mores Geddam</a
              >
            </td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/department-of-management/MTQwNQ=="
                target="_blank"
                >Dr Tejaswini Bastray</a
              >
            </td>
            <td >I</td>
            <td >KA-22-001</td>
            <td >
              <a href="mailto:nssunit1@sju.edu.in">nssunit1@sju.edu.in</a>
            </td>
          </tr>
          <tr>
            <th scope="row" >2</th>
            <td >SJU NSS UNIT 2</td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/advanced-computing/MTA5MA=="
                target="_blank"
                >Dr Asha K</a
              >
            </td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/statistics/MTA2Mw=="
                target="_blank"
                >Mr Ebenezer</a
              >
            </td>
            <td >I</td>
            <td >KA-22-002</td>
            <td >
              <a href="mailto:nssunit2@sju.edu.in">nssunit2@sju.edu.in</a>
            </td>
          </tr>
          <tr>
            <th scope="row" >3</th>
            <td >SJU NSS UNIT 3</td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/zoology/MTQxNA=="
                target="_blank"
                >Dr Magesh D</a
              >
            </td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/mathematics/MTA3Nw=="
                target="_blank"
                >Dr Gerard Rozario</a
              >
            </td>
            <td >II</td>
            <td >KA-22-003</td>
            <td >
              <a href="mailto:nssunit3@sju.edu.in">nssunit3@sju.edu.in</a>
            </td>
          </tr>
          <tr>
            <th scope="row" >4</th>
            <td >SJU NSS UNIT 4</td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/hindi/MTI3NA=="
                target="_blank"
                >Dr Gurudata</a
              >
            </td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/computer-science-and-computer-application/MTU3OQ=="
                target="_blank"
                >Mr Mohammad Mueen Pasha</a
              >
            </td>
            <td >II</td>
            <td >KA-22-004</td>
            <td >
              <a href="mailto:nssunit4@sju.edu.in">nssunit4@sju.edu.in</a>
            </td>
          </tr>
          <tr>
            <th scope="row" >5</th>
            <td >SJU NSS UNIT 5</td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/History/MTE5Mg=="
                target="_blank"
                >Mr Mahesh DK</a
              >
            </td>
            <td >
              <a
                href="https://www.sju.edu.in/faculty-details/st-joseph-university/Sociology/MTE5Mw=="
                target="_blank"
                >Dr Chandini B</a
              >
            </td>
            <td >III</td>
            <td >KA-22-005</td>
            <td >
              <a href="mailto:nssunit5@sju.edu.in">nssunit5@sju.edu.in</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Footer and logos -->
    <?php include "footer.php" ?>

    <script src="assets/js/custom.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
