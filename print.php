<?php
include 'auth/auth.php';
include 'config.php';
// Define number of results per page
$resultsPerPage = 100;
// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;
// Retrieve search parameter
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
// Retrieve application number if set
$applicationNo = isset($_GET['application_no']) ? $_GET['application_no'] : null;
// Modify SQL query to fetch student data based on application number or search term
if ($applicationNo) {
    // If application number is set, fetch data for that specific application
    $query = "
        SELECT
            s.id AS student_id,
            s.name AS student_name,
            s.application_no AS application_number,
            s.course AS student_course,
            s.college AS student_college,
            s.gender AS student_gender,
            b.bed_number AS bed_number,
            r.room_number AS room_number,
            s.hostel_id AS hostel_name,
            r.floor_number as floor_number,
            b.is_occupied as alloted
        FROM
            students s
        LEFT JOIN
            beds b ON s.id = b.student_id
        LEFT JOIN
            rooms r ON b.room_id = r.id
        LEFT JOIN 
            hostels h ON r.hostel_id=h.id
        WHERE
            s.application_no = :application_no
        LIMIT :offset, :resultsPerPage;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':application_no', $applicationNo, PDO::PARAM_STR);
} else {
    // If no application number, perform search
    $query = "
        SELECT
            s.id AS student_id,
            s.name AS student_name,
            s.application_no AS application_number,
            s.course AS student_course,
            s.college AS student_college,
            s.gender AS student_gender,
            b.bed_number AS bed_number,
            r.room_number AS room_number,
            s.hostel_id AS hostel_name,
            r.floor_number as floor_number,
            b.is_occupied as alloted
        FROM
            students s
        LEFT JOIN
            beds b ON s.id = b.student_id
        LEFT JOIN
            rooms r ON b.room_id = r.id
        LEFT JOIN 
            hostels h ON r.hostel_id=h.id
        WHERE
            s.name LIKE :search
            OR s.application_no LIKE :search
        ORDER BY
            s.id
        LIMIT :offset, :resultsPerPage;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':resultsPerPage', $resultsPerPage, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Query to get total number of records for pagination (only when not filtering by application number)
if (!$applicationNo) {
    $countQuery = "
        SELECT COUNT(*) AS total
        FROM students s
        LEFT JOIN
            beds b ON s.id = b.student_id
        LEFT JOIN
            rooms r ON b.room_id = r.id
        LEFT JOIN 
            hostels h ON r.hostel_id=h.id
        WHERE
            s.name LIKE :search
            OR s.application_no LIKE :search
            OR s.course LIKE :search
            OR s.college LIKE :search
            OR s.gender LIKE :search
            OR r.room_number LIKE :search
            OR b.bed_number LIKE :search;
    ";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $countStmt->execute();
    $totalResults = $countStmt->fetchColumn();
    $totalPages = ceil($totalResults / $resultsPerPage);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Allocations</title>
    <?php include 'includes/head.php'; ?>
    <style>
        tr td:first-child {
            font-weight: bolder;
        }
    </style>
</head>

<body>
    <div class="container mt-5">


        <?php
        $hostelMap = [
            "2" => "Boys Hostel",
            "3" => "Girls Hostel",
            "4" => "Another Hostel - A Block",
            "5" => "Another Hostel - A Block Boys",
            "6" => "Krishna BOYS HOSTEL",
            "7" => "MISHRA BOYS HOSTEL",
            "1" => "BALAJI BOYS HOSTEL",
            "0" => ""
        ];
        $FloorMap = [
            "GF" => "Ground Floor",
            "FF" => "First Floor",
            "SF" => "Second Floor",
            "TF" => "Third Floor",


        ];
        foreach ($results as $row): ?>
            <tr>

                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Electronic Receipt</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    html {
                        font-size: 12px !important;
                    }
                </style>

                <div class="container p-4 border  ">
                    <div class="text-center mb-4">
                        <img src="assets/logo.png" height="120" alt="Logo" class="  mb-2">
                        <h1 class="h4"><?php echo htmlspecialchars($row['student_college']); ?></h1>
                        <p class="mt-3"><strong>Application Number : </strong> <?php echo htmlspecialchars($row['application_number']); ?></p>
                    </div>
                    <table class="table table-bordered ">
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            </tr>
                            <tr>
                                <td>Course</td>
                                <td><?php echo htmlspecialchars($row['student_course']); ?></td>

                            </tr>
                            <tr>
                                <td>College</td>
                                <td><?php echo htmlspecialchars($row['student_college']); ?></td>

                            </tr>
                            <tr>
                                <td>Hostel Name</td>
                                <td><?php echo htmlspecialchars($hostelMap[$row['hostel_name']]); ?></td>

                            </tr>
                            <tr>
                                <td>Floor</td>
                                <td><?php echo htmlspecialchars($FloorMap[$row['floor_number']]); ?></td>

                            </tr>
                            <tr>
                                <td>Room Number</td>
                                <td><?php echo htmlspecialchars($row['room_number']); ?></td>

                            </tr>
                            <tr>
                                <td>Bed</td>
                                <td><?php echo htmlspecialchars($row['bed_number']); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-muted small">
                        <p>* This is a system generated receipt and does not require the seal & signature.</p>

                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                
                    </tr>
                <?php endforeach; ?>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>