<?php

include 'auth/auth.php';
include 'config.php';
// SQL query
$query = "
    SELECT
        s.id AS hostel_id,
        s.name AS hostel_name,
        s.floors AS floors,
        s.rooms AS rooms,
        s.seats AS seats,
        s.gender as gender,
        s.campus_type as campus_type
    FROM
        hostels s
    ORDER BY
        s.id;
        
";

// Execute the query
$stmt = $pdo->query($query);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostels</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
 <?php include 'includes/head.php'; ?> </head>  

<body> <?php include 'includes/header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Hostel Allocations</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Floors</th>
                    <th>Rooms</th>
                    <th>Seats</th>
                    <th>gender</th>
                    <th>Campus Type</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['hostel_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['hostel_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['floors']); ?></td>
                        <td><?php echo htmlspecialchars($row['rooms']); ?></td>
                        <td><?php echo htmlspecialchars($row['seats']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['campus_type']); ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>