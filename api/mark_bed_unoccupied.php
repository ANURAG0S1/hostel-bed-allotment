<?php
include '../config.php';

if (isset($_GET['bed_id'])) {
    $bedId = $_GET['bed_id'];

    // Check if the bed has an associated student ID
    $checkQuery = "SELECT student_id FROM beds WHERE id = :bed_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([':bed_id' => $bedId]);
    $bed = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($bed) {
        if ($bed['student_id'] !== null && $bed['student_id'] !== '') {
            // Fetch the application number of the student
            $studentId = $bed['student_id'];
            $studentQuery = "SELECT application_no, name FROM students WHERE id = :student_id";
            $studentStmt = $pdo->prepare($studentQuery);
            $studentStmt->execute([':student_id' => $studentId]);
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

            // Display error message with application number
            $applicationNo = $student ? $student['application_no'] : 'Unknown';
            $student_name = $student ? $student['name'] : 'Unknown';
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Error</title>
                <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="alert alert-danger" role="alert">
                        Error: This bed cannot be marked as occupied because it is already assigned to a student with Application No: ' . htmlspecialchars($applicationNo) . '  (' .  htmlspecialchars($student_name) . '.)
                    </div>
                    <a href="../beds.php" class="btn btn-primary">Go Back</a>
                    <a href="../index.php?search=' . htmlspecialchars($applicationNo) . '" class="btn btn-primary">Check assigned student</a>
                </div>
            </body>
            </html>';
            exit();
        } else {
            // Bed does not have a student ID, mark it as unoccupied
            $updateQuery = "UPDATE beds SET is_occupied = 0 WHERE id = :bed_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([':bed_id' => $bedId]);

            // echo '<!DOCTYPE html>
            // <html lang="en">
            // <head>
            //     <meta charset="UTF-8">
            //     <meta name="viewport" content="width=device-width, initial-scale=1.0">
            //     <title>Success</title>
            //     <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            // </head>
            // <body>
            //     <div class="container mt-5">
            //         <div class="alert alert-success" role="alert">
            //             The bed has been marked as unoccupied successfully.
            //         </div>
            //         <a href="../beds.php" class="btn btn-primary">Go Back</a>
            //     </div>
            // </body>
            // </html>';
            header('Location: ../beds.php');
            exit();
        }
    } else {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="alert alert-danger" role="alert">
                    Error: Bed not found.
                </div>
                <a href="../beds.php" class="btn btn-primary">Go Back</a>
            </div>
        </body>
        </html>';
        exit();
    }
} else {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">
                Error: No bed ID provided.
            </div>
            <a href="../beds.php" class="btn btn-primary">Go Back</a>
        </div>
    </body>
    </html>';
    exit();
}
