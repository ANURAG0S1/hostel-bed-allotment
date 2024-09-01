<?php include 'auth/auth.php'; 
include 'config.php';

// Retrieve search parameters
$hostelName = isset($_GET['hostel_name']) ? $_GET['hostel_name'] : '';
$roomNumber = isset($_GET['room_number']) ? $_GET['room_number'] : '';
$floor = isset($_GET['floor']) ? $_GET['floor'] : '';

// SQL query to fetch room data with search parameters
$query = "
    SELECT
        r.id AS room_id,
        r.room_number,
        r.capacity AS room_capacity,
        r.floor,
        h.name AS hostel_name
    FROM
        rooms r
        JOIN hostels h ON r.hostel_id = h.id
    WHERE
        (:hostel_name = '' OR h.name LIKE :hostel_name)
        AND (:room_number = '' OR r.room_number LIKE :room_number)
        AND (:floor = '' OR r.floor LIKE :floor)
    ORDER BY
        r.hostel_id, r.floor, r.room_number;
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':hostel_name' => '%' . $hostelName . '%',
    ':room_number' => '%' . $roomNumber . '%',
    ':floor' => '%' . $floor . '%'
]);

$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <?php include 'includes/head.php'; ?>
</head>

<body> <?php include 'includes/header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Room Data</h1>

        <!-- Search Form -->
        <form method="GET" action="">
            <div class="form-row mb-3">
                <div class="col">
                    <input type="text" name="hostel_name" class="form-control" placeholder="Hostel Name" value="<?php echo isset($_GET['hostel_name']) ? htmlspecialchars($_GET['hostel_name']) : ''; ?>">
                </div>

                <div class="col">
                    <input type="text" name="floor" class="form-control" placeholder="Floor Number" value="<?php echo isset($_GET['floor']) ? htmlspecialchars($_GET['floor']) : ''; ?>">
                </div>
                <div class="col">
                    <input type="text" name="room_number" class="form-control" placeholder="Room Number" value="<?php echo isset($_GET['room_number']) ? htmlspecialchars($_GET['room_number']) : ''; ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="rooms.php" class="btn btn-secondary">Reset</a> <!-- Replace 'your_page.php' with the actual filename -->
                </div>
            </div>
        </form>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S No</th>

                    <th>Room Number</th>
                    <th>Room Capacity</th>
                    <th>Floor Number</th>
                    <th>Hostel Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $key => $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key + 1); ?></td>

                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($room['room_capacity']); ?></td>
                        <td><?php echo htmlspecialchars($room['floor']); ?></td>
                        <td><?php echo htmlspecialchars($room['hostel_name']); ?></td>
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