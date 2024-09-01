<?php
include '../config.php';
// Get the gender parameter from the request, if provided
$gender = isset($_GET['gender']) ? $_GET['gender'] : null;

// Prepare the SQL query to fetch unallotted beds
$query = "
SELECT
    b.id AS bed_id,
    b.bed_number,
    r.room_number,
    r.floor_number,
    r.capacity,
    h.gender,
    b.is_occupied,
    h.name AS hostel_name,
    h.id AS hostel_number
FROM
    beds b
JOIN
    rooms r ON b.room_id = r.id 
LEFT JOIN
	hostels h ON r.hostel_id= h.id
ORDER BY `bed_id` DESC ;
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

    // Return the results as JSON

    echo json_encode($beds);
} catch (PDOException $e) {
    // Return an error response if the query fails

    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
