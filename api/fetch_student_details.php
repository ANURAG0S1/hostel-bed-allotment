<?php
 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
 
 include '../config.php';

// Get the application number from the POST request
$application_no = $_POST['application_no'];

// Prepare the SQL query to fetch student details
$query = "
    SELECT
        s.name,
        s.course,
        s.college,
        s.gender,
        s.id,
        s.hostel_id
 
    FROM
        students s
    WHERE
        s.application_no = ?
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$application_no]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
       
        echo json_encode($student);
    } else {
       
        echo json_encode(['error' => 'No student found with the provided application number.']);
    }
} catch (PDOException $e) {
   
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
