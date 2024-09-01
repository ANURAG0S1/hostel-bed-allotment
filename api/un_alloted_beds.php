<?php
// Database connection details
include '../config.php';

// Get the gender parameter from the request, if provided
$gender = isset($_GET['gender']) ? $_GET['gender'] : null;

// Prepare the SQL query to fetch unallotted beds
$query = "
  SELECT
    b.id AS bed_id,
    b.bed_number,
    r.room_number,
    f.floor_number,
    r.capacity,
    h.gender,
    b.is_occupied,
    h.name AS hostel_name,
    h.id as hostel_id
FROM
    beds b
JOIN
    rooms r ON b.room_id = r.id
JOIN
    floors f ON r.floor_number = f.floor_number AND r.hostel_id = f.hostel_id
JOIN
    hostels h ON r.hostel_id = h.id
WHERE
    b.is_occupied = 0
     
ORDER BY
    h.name, f.floor_number, r.room_number, b.bed_number;


";

// Add a condition for gender if the parameter is provided
if ($gender) {
    $query .= " AND h.gender = :gender";
}

$query .= " ORDER BY h.name, f.floor_number, r.room_number, b.bed_number";

try {
    $stmt = $pdo->prepare($query);

    // Bind the gender parameter if it exists
    if ($gender) {
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    }

    $stmt->execute();
    $beds = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    echo json_encode($beds);
} catch (PDOException $e) {
   
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
