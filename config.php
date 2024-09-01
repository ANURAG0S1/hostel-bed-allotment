<?php
// Database connection details
$host = '127.0.0.1';
$db = 'hostel_management';
$user = 'hostel_db';
$pass = 'password';
// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Return an error response if the database connection fails
    header('Content-Type: application/json');
    // echo json_encode(['error' => 'Could not connect to the database: ' . $e->getMessage()]);
    exit();
}
