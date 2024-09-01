<!-- update_hostel_process.php -->
<?php
include '../config.php';


// Retrieve form data
$studentId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
$hostelId = isset($_POST['hostel_name']) ? (int)$_POST['hostel_name'] : 0;
 

if ($studentId && $hostelId) {

    try {
        // Update the student's hostel_id
        $stmt = $pdo->prepare("UPDATE students SET hostel_id = :hostel_id WHERE id = :student_id");
        $stmt->execute([
            ':hostel_id' => $hostelId,
            ':student_id' => $studentId
        ]);

        // Redirect back with a success message
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bed Assignment Success</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
          </head>  
        <body> 
        
        <div class="d-flex-col vh-100" style="margin-top:220px;">
        <div class="m-auto text-center">
     <h1 class="  fw-bold text-success" style="    font-size: 4.5rem;
    line-height: 1.2;">Congratulations</h1>
            <h2 class="display-5">Hostel Assigned Successfully</h2>
        </div>

    <div class="row center  w-50 mx-auto justify-content-around" style="margin-top:40px">
            <div class="text-center mt-4 mx-5">
            <a href="../index.php" class="btn btn-primary"> Hostel Allotment Status</a>
            </div>
             <div class="text-center mt-4 mx-5">
            <a href="../allotment.php" class="btn btn-success">Proceed For Room Allotment</a>
            </div>
            </div>
    
    </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
    } catch (Exception $e) {
        // Redirect back with an error message if needed
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bed Assignment Success</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
         </head>  
        <body> 
        <div class="alert alert-error alert-dismissible fade show mt-4" role="alert">
            <strong> error
            <a href="../index.php" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
        </div>  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
    }
    exit;
} else {
    echo " byee";
    // Redirect back with an error message if needed
    echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bed Assignment Success</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
         </head>  
        <body> 
        <div class="alert alert-error alert-dismissible fade show mt-4" role="alert">
            <strong> error
            <a href="../index.php" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
        </div> <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
    exit;
}
