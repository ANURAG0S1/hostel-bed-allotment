<?php include 'auth/auth.php'; ?>
<?php

include 'config.php';

// Get search parameters from the form
$searchBedNumber = isset($_GET['bed_number']) ? $_GET['bed_number'] : '';
$searchHostelName = isset($_GET['hostel_name']) ? $_GET['hostel_name'] : '';
$isOccupied = isset($_GET['is_occupied']) ? $_GET['is_occupied'] : '';
$searchRoomNumber = isset($_GET['room_number']) ? $_GET['room_number'] : '';

// Prepare the SQL query with placeholders
$query = "
    SELECT
        b.id AS bed_id,
        b.room_id,
        b.bed_number,
        b.is_occupied,
        h.name AS hostel_name,
        r.room_number AS room_number,
        r.floor_number AS floor
    FROM
        beds b
    JOIN rooms r ON b.room_id = r.id
    JOIN hostels h ON h.id = r.hostel_id
    WHERE
        b.bed_number LIKE :bed_number
        AND h.name LIKE :hostel_name
        AND r.room_number LIKE :room_number
";

// Add the condition for `is_occupied` if a specific option is selected
if ($isOccupied !== '') {
    if ($isOccupied == '1') {
        $query .= " AND b.is_occupied = 1";
    } elseif ($isOccupied == '0') {
        $query .= " AND b.is_occupied = 0";
    }
}

$query .= " ORDER BY b.room_id";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':bed_number' => '%' . $searchBedNumber . '%',
    ':hostel_name' => '%' . $searchHostelName . '%',
    ':room_number' => '%' . $searchRoomNumber . '%'
]);
$beds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beds Information</title>
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Beds Information with Hostel Name</h1>

        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="form-row row">

                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="hostel_name" name="hostel_name" placeholder="Hostel Name" value="<?php echo htmlspecialchars($searchHostelName); ?>">
                </div>
                <div class="form-group col-md-2">
                    <input type="text" class="form-control" id="room_number" name="room_number" placeholder="Room Number" value="<?php echo htmlspecialchars($searchRoomNumber); ?>">
                </div>
                <div class="form-group col-md-2">
                    <input type="text" class="form-control" id="bed_number" name="bed_number" placeholder="Bed Number" value="<?php echo htmlspecialchars($searchBedNumber); ?>">
                </div>
                <div class="form-group col-md-2">
                    <select class="form-control" id="is_occupied" name="is_occupied">
                        <option value="">All</option>
                        <option value="1" <?php echo $isOccupied === '1' ? 'selected' : ''; ?>>Occupied</option>
                        <option value="0" <?php echo $isOccupied === '0' ? 'selected' : ''; ?>>Available</option>
                    </select>
                </div>
                <div class="form-group col-md-2">

                    <button type="submit" class="btn btn-primary btn-block w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S no</th>
                        <th>Hostel Name</th>
                        <th>Floor</th>
                        <th>Room No.</th>
                        <th>Bed Number</th>
                        <th>Room Status</th>
                        <?php if ($_SESSION['username'] == 'meadmin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($beds as $key => $bed): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($key + 1); ?></td>
                            <td><?php echo htmlspecialchars($bed['hostel_name']); ?></td>
                            <td><?php echo htmlspecialchars($bed['floor']); ?></td>
                            <td><?php echo htmlspecialchars($bed['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($bed['bed_number']); ?></td>
                            <td><?php echo $bed['is_occupied'] ? 'Occupied' : 'Available'; ?></td>
                            <?php if ($_SESSION['username'] == 'meadmin'): ?>
                                <td>
                                    <?php if ($bed['is_occupied'] == 0): ?>
                                        <!-- Bed is unoccupied, show button to mark as occupied -->
                                        <a href="api/mark_bed_occupied.php?bed_id=<?php echo htmlspecialchars($bed['bed_id']); ?>" class="btn btn-warning">Mark as Occupied</a>
                                    <?php else: ?>
                                        <!-- Bed is occupied, show button to mark as unoccupied -->
                                        <a href="api/mark_bed_unoccupied.php?bed_id=<?php echo htmlspecialchars($bed['bed_id']); ?>" class="btn btn-secondary">Mark as Unoccupied</a>
                                    <?php endif; ?>
                                </td><?php endif; ?>


                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>