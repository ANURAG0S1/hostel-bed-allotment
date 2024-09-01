<?php include 'auth/admin_only.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Bed Assignment</title>
    <?php include 'includes/head.php' ?>
</head>
<body>
    <?php include 'includes/header.php' ?>
    <div class="container mt-5">
        <h2 class="mb-4">Remove Bed Assignment</h2>
        <!-- Form to enter application number -->
        <?php
        // Retrieve the application_no from the URL if it exists
        $application_no = isset($_GET['application_no']) ? htmlspecialchars($_GET['application_no'], ENT_QUOTES, 'UTF-8') : '';
        ?>
        <form method="post" class="row" action="">
            <div class="form-group col-md-8">
                <input type="text" class="form-control" id="application_no" name="application_no" placeholder="Reference Number" value="<?php echo $application_no; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary col-md-4">Fetch Student Details</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include 'config.php';
            // Sanitize and validate input
            $application_no = filter_input(INPUT_POST, 'application_no', FILTER_SANITIZE_STRING);
            // Fetch student details based on application number
            $query = "SELECT 
    s.id AS student_id, 
    s.name AS student_name, 
    h.name AS hostel_name, 
    b.bed_number AS bed_number, 
    b.id AS bed_id,
    r.room_number
FROM 
    students s
JOIN 
    beds b ON s.id = b.student_id
JOIN 
    hostels h ON s.hostel_id = h.id
JOIN 
    rooms r ON b.room_id = r.id
WHERE 
    s.application_no = ?
";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$application_no]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                // Display student details
                echo '
            <div class="mt-4">
                <h4>Student Details</h4>
                <p><strong>Reference Number : </strong> ' . htmlspecialchars($application_no, ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Name : </strong> ' . htmlspecialchars($student['student_name'], ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Hostel : </strong> ' . htmlspecialchars($student['hostel_name'], ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Room Number : </strong> ' . htmlspecialchars($student['room_number'], ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Bed : </strong> ' . htmlspecialchars($student['bed_number'], ENT_QUOTES, 'UTF-8') . '</p>
            </div>';
                // Show delete button
                echo '
            <form method="post" action="api/remove_bed_assignment.php">
                <input type="hidden" name="student_id" value="' . htmlspecialchars($student['student_id'], ENT_QUOTES, 'UTF-8') . '">
                <input type="hidden" name="bed_id" value="' . htmlspecialchars($student['bed_id'], ENT_QUOTES, 'UTF-8') . '">
                <button type="submit" class="btn btn-danger mt-3">Remove Bed Assignment</button>
            </form>';
            } else {
                echo '<div class="alert alert-danger mt-4">No Bed Assignment found with that application number.</div>';
            }
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
