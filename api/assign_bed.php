<?php
include '../config.php';

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $application_no = filter_input(INPUT_POST, 'ref_id');
    $bed_id = filter_input(INPUT_POST, 'bed_id', FILTER_VALIDATE_INT);
    $hostel_id = filter_input(INPUT_POST, 'hostel_id', FILTER_VALIDATE_INT);

    if ($student_id === false || $bed_id === false || $hostel_id === false) {
        die('Invalid input data.');
    }

    // Check if the student is already assigned to another bed
    $query = "SELECT COUNT(*) FROM beds WHERE student_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$student_id]);
    $studentAssignmentCount = $stmt->fetchColumn();

    if ($studentAssignmentCount > 0) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bed Assignment Error</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
           
        </head>
        <body>
        <div class="container mt-5">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> The student is already assigned to another bed.
                <a href="../index.php" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
            </div>
            <div class="text-center mt-4">
                <a href="../index.php?search='. htmlspecialchars($application_no).'" class="btn btn-primary">Check Student Allotment</a>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
        exit;
    }

    // Check if the bed is already occupied
    $query = "SELECT is_occupied FROM beds WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$bed_id]);
    $bed = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bed === false) {
        die('Bed not found.');
    }

    if ($bed['is_occupied']) {
        die('The selected bed is already occupied.');
    }

    // Fetch the student name
    $query = "SELECT name FROM students WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student === false) {
        die('Student not found.');
    }

    // Start transaction
    try {
        $pdo->beginTransaction();

        // Update bed record to assign it to the student
        $query = "UPDATE beds SET is_occupied = 1, student_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$student_id, $bed_id]);

        // Update student's hostel_id
        $stmt = $pdo->prepare("UPDATE students SET hostel_id = :hostel_id WHERE id = :student_id");
        $stmt->execute([
            ':hostel_id' => $hostel_id,
            ':student_id' => $student_id
        ]);

        // Commit transaction
        $pdo->commit();

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
            <h2 class="display-5">  Bed assigned successfully to ' . htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') . '.</h2>
        </div>

    <div class="row center  w-50 mx-auto justify-content-around" style="margin-top:40px">
             
             <div class="text-center mt-4 mx-5">
            <a href="../print.php?application_no=' . htmlspecialchars($application_no, ENT_QUOTES, 'UTF-8') . '" class="btn btn-success">Print Receipt</a>
            </div>
             <div class="text-center mt-4 mx-5">
            <a href="../index.php?search='. htmlspecialchars($application_no, ENT_QUOTES, 'UTF-8') .'" class="btn btn-primary">Check Allotment</a>
            </div>
            </div>
    
    </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo 'Failed to assign bed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    echo 'Invalid request method.';
}
