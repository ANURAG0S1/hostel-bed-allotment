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

// SQL query to fetch student data with search parameter
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
        OR s.course LIKE :search
        OR s.college LIKE :search
        OR s.gender LIKE :search
        OR r.room_number LIKE :search
        OR b.bed_number LIKE :search
    ORDER BY
        s.id
    LIMIT :offset, :resultsPerPage;
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':resultsPerPage', $resultsPerPage, PDO::PARAM_INT);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to get total number of records for pagination
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Allocations</title>

    <?php include 'includes/head.php'; ?>
    <style>
        td .btn {
            min-width: 120px;
        }

        td:last-child {
            text-align: center;
        }

        tr:hover {
            background: #dadada !important;
        }

        .table>:not(caption)>*>* {
            background-color: transparent !important;
        }
    </style>

</head>

<body> <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Student Allocations</h1>

        <!-- Search Form -->
        <form method="GET" action="" class="mb-4">
            <div class="form-row row w-100">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                <div class="row col-md-6 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary col-md-5 mx-4">Search</button>
                    <a href="index.php" class="btn btn-secondary col-md-5 container fluid">Reset</a> <!-- Replace 'index.php' with the actual filename if different -->
                </div>
            </div>
        </form>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>REF Number</th>
                    <th>Course</th>
                    <th>College</th>
                    <th>Gender</th>
                    <th>Hostel</th>
                    <th>Floor</th>
                    <th>Room</th>
                    <th>Bed</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
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

                foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['application_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_course']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_college']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_gender']); ?></td>
                        <td><?php echo htmlspecialchars($hostelMap[$row['hostel_name']]); ?></td>
                        <td><?php echo htmlspecialchars($row['floor_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['bed_number']); ?></td>
                        <td>
                            <?php
                            $dynamicValue = $row['application_number'];
                            $bedNumber = $row['bed_number'];
                            if ($bedNumber) : ?>
                                <span onclick="recipt('<?php echo htmlspecialchars($row['application_number']); ?>')" class="btn btn-primary mb-1"> Print</span>
                                <?php if ($_SESSION['username'] == 'meadmin'): ?>
                                    <a href="delete.php?application_no=<?php echo urlencode($dynamicValue); ?>" class="btn btn-danger">deallocate</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="allotment.php?application_no=<?php echo urlencode($dynamicValue); ?>" class="btn btn-success">Allot Room</a>

                            <?php endif; ?>

                        </td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <script>
        function recipt(e) {

            window.open('print.php?application_no=' + e)
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>