<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $bed_id = filter_input(INPUT_POST, 'bed_id', FILTER_VALIDATE_INT);

    if ($student_id === false || $bed_id === false) {
        die('Invalid input data.');
    }

    try {
        $pdo->beginTransaction();

        // Update bed record to unassign it from the student
        $query = "UPDATE beds SET is_occupied = 0, student_id = NULL WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$bed_id]);

        // Optionally, update the student's hostel_id to NULL
        $query = "UPDATE students SET hostel_id = 0 WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$student_id]);

        // Commit transaction
        $pdo->commit();

        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bed Unassignment Success</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <div class="container mt-5">
            <div class="alert alert-success">
                <strong>Success!</strong> Bed unassigned successfully from the student.
            </div>
            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo 'Failed to unassign bed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    echo 'Invalid request method.';
}
