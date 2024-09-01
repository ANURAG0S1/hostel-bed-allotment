<?php
include '../config.php';




// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $bed_id = filter_input(INPUT_POST, 'bed_id', FILTER_VALIDATE_INT);

    if ($bed_id === false) {
        die('Invalid bed ID.');
    }

    // Check if the bed exists
    $query = "SELECT is_occupied FROM beds WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$bed_id]);
    $bed = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bed === false) {
        die('Bed not found.');
    }

    // Update bed record to mark it as occupied
    $query = "UPDATE beds SET is_occupied = 1 WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$bed_id]);

    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bed Status Update</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="d-flex-col vh-100" style="margin-top:220px;">
        <div class="m-auto text-center">
            <h1 class="fw-bold text-success" style="font-size: 4.5rem; line-height: 1.2;">Success</h1>
            <h2 class="display-5">Bed status updated successfully.</h2>
        </div>
        <div class="row center w-50 mx-auto justify-content-around" style="margin-top:40px">
            <div class="text-center mt-4 mx-5">
                <a href="../index.php" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
} else {
    echo 'Invalid request method.';
}
