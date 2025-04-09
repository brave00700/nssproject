<?php
require_once __DIR__ . "/../tcpdf/tcpdf.php";

require_once __DIR__ . '/functions.php';

// Check current session
$reg = checkSession();
$unit = $_SESSION['unit'];

$message = "";
$status = "";
$credits = 0;



// Create a connection object 
$conn = getDatabaseConnection();
$stmt3 = $conn->prepare("SELECT status, credits FROM credits WHERE register_no = ?");
$stmt3->bind_param("s", $reg);
$stmt3->execute();
$result3 = $stmt3->get_result();

if($result3->num_rows > 0){
    $row1 = $result3->fetch_assoc();
    $status = $row1['status'];
    $credits = intval($row1['credits']);
}
$stmt = $conn->prepare("SELECT events.event_duration 
FROM attendance
JOIN events on attendance.event_id = events.event_id
WHERE register_no = ? AND attendance.status = 'APPROVED'");
$stmt->bind_param("s", $reg);
$stmt->execute();
$result = $stmt->get_result();

//Creating a variable to count number of hours attended by the student
$hours = 0;

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {
        $hours += $row['event_duration'];
    }
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if(isset($_POST['credit'])){
    
        $credits = intval($hours / 30);

        $stmt2 = $conn->prepare("INSERT INTO credits(register_no, credits, hours) VALUES(?, ?, ?)");
        $stmt2->bind_param("ssi", $reg, $credits, $hours);
        if($stmt2->execute()){
            header("Location: credits.php");
        }
        $stmt->close();
        $conn->close();
    }else if(isset($_POST['generate'])){

        $stmt4 = $conn->prepare("SELECT credits, hours, credits.register_no, students.name, students.unit 
        FROM credits 
        JOIN students on credits.register_no = students.user_id
        WHERE credits.register_no = ?");
        $stmt4->bind_param("s", $reg);
        $stmt4->execute();
        $result4 = $stmt4->get_result();

        if($result4->num_rows > 0){
            $row = $result4->fetch_assoc();
            $hours = $row['hours'];
            $credits = $row['credits'];
            $name = $row['name'];
            $unit = $row['unit'];
        }


        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', 'B', 26);
        $pdf->SetXY(58,16);
        $pdf->Cell(130, 10, "ST JOSEPH'S UNIVERSITY, BENGALURU", 0, 0);

        $pdf->SetFont('times', '', 20);
        $pdf->SetXY(69, 27);
        $pdf->Cell(130, 10, "NATIONAL SERVICE SCHEME (UNIT {$unit}) 2023-2024", 0, 0);

        $pdf->Line(70, 47, 235, 47); // Line from (10,50) to (200,50)

        
        $pdf->SetFont('dejavusansextralight', '', 24);
        $pdf->SetXY(84, 55);
        $pdf->Cell(130, 10, "CERTIFICATE OF APPRECIATION", 0, 0);

        // **Double Border**
        $pdf->SetDrawColor(50, 40, 100);
        $pdf->SetLineWidth(4);
        $pdf->Rect(5, 5, 287, 200);

        $pdf->SetDrawColor(255, 0, 0);
        $pdf->SetLineWidth(2);
        $pdf->Rect(10, 10, 277, 190);

        // **Logos**
        $pdf->Image('../assets/icons/sju_logo.jpg', 17, 15, 29);
        $pdf->Image('../assets/icons/nss_logo.jpg', 252, 15, 29);

        $pdf->SetFont('pdfatimes', '', 17);
        $pdf->SetY(70);
        // Write HTML to PDF
        $html = '<div style = "text-align: center"><p>This is to certify that <b>' . $name . ' (' . $reg . ')</b>  of <b>NSS Unit ' . $unit . '</b> has completed </p>
            <p>N.S.S volunteering for the academic year 2023 to 2024 and has done satisfactory work.</p>
            </div>';
        $pdf->writeHTML($html, true, false, true, false, '');

        //------------------------------------------

        $pdf->SetFont('pdfatimes', '', 16);

        $pdf->SetXY(30, 140);
        $pdf->Cell(130, 10, 'Hours Spent:', 0, 0);

        $pdf->SetXY(-60, 140);
        $pdf->Cell(130, 10, 'Credits:', 0, 0);

        $pdf->SetFont('pdfatimes', 'B', 16);

        $pdf->SetXY(63, 140);
        $pdf->Cell(130, 10, $hours, 0, 0);

        $pdf->SetXY(-39, 140);
        $pdf->Cell(130, 10, $credits, 0, 0);

        //----------------------------------------

        $pdf->SetFont('pdfatimes', '', 16);

        $pdf->SetXY(20, -25);
        $pdf->Cell(130, 10, 'Date: ' . date('d / m / Y'), 0, 0);

        $pdf->SetXY(-88, -25);
        $pdf->Cell(130, 10, 'Signature of Program Officer', 0, 0);

        // Output PDF
        $pdf->Output('certificate.pdf', 'I'); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - NSS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    .tile {
        border: 1px solid #303a8394;
        padding: 10px;
        display: flex;
        margin-bottom: 10px;
        background-color: #303983;
        color: #FFFFFF;
        border-radius: 5px;
    }
    .tile a {
        flex: 0.2;
        display: inline-block;
        text-decoration: none;
    }
    .tile img {
        display: block;
        height: 100%;
        width: 100%;
        object-fit: fill;
    }
    .tile-content{
        flex:0.8;
        padding: 0 5px;
        display: flex;
        flex-direction: column;
    }
    span{
        line-height: 1.1rem;
        overflow-y: hidden;
        font-size: 1rem;
        flex: 1;
    }
    span.e_name {
        font-size: 1.6rem;
        flex: none;
        line-height: 1.6rem;
    }
    span.e_name span {
        text-transform:uppercase;
        font-size: inherit;
    }
    input[name="subject"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
    outline: none;
}
.widget{
    width: 100%;
    padding-left: 30px;
    padding-right: 30px;
    min-height: 50vh;
}

/* Basic style for the dropdown */
select {
    width: 100%;  /* Makes it responsive */
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f8f8f8;
    cursor: pointer;
    appearance: none;  /* Removes default dropdown arrow */
    -webkit-appearance: none;
    -moz-appearance: none;
}

/* Custom dropdown arrow */
select::after {
    content: " ▼";
    font-size: 12px;
    color: #007bff;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}

/* Hover and Focus state */
select:hover, select:focus {
    border-color: #007bff;
    outline: none;
}

.widget {
    width: 80%;
    max-width: 600px;
    margin: 40px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.widget p {
    font-size: 18px;
    color: #333;
    margin: 10px 0;
}

button.credit, button.generate {
    width: 100%;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

button.credit {
    background: rgb(255, 164, 6);
    color: white;
}

button.credit:hover {
    background: #e69202;
}

button.generate {
    color: white;
    margin-top: 10px;
    background:rgb(255, 164, 6);
}

button.generate:hover {
    background: #e69202;
}



</style>
</head>
<body>
<?php include "header.php" ?>
   
<div class="nav">
        <div class="ham-menu">
            <a><i class="fa-solid fa-bars ham-icon"></i></a>
        </div>
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="attendance_view.php">Attendance</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="grievance.php">Grievience</a></li>
            <li><a  class="active" href="credits.php">Credits</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="widget">
            
            <?php
            if(empty($status)){
                echo '<form method="POST">
                <button type="submit" name="credit" class="credit">Claim Credits</button>
            </form>';
            }
            else{
                // Show hours after form submission
                if($status == 'PO_APPROVED') $status = 'Approved by Program Officer';
                echo "<p>Working Hours: " . $hours . " hours.</p>
                <p>Credits Claimable: " . $credits . "</p>
                <p>Approval Status: " . $status . "</p>";
            }
            if($message){
                echo $message;
            }
            if($status == "APPROVED"){
                echo '<form method="POST">
                <button type="submit" name="generate" class="generate">Get Certificate</button>
            </form>';
            }
            ?>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>