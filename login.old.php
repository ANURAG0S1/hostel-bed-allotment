<?php
// Start output buffering
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'config.php'; // Include your PDO database connection script

// Define log file path
$logFile = 'logs/login_debug.log';

// Helper function to write log messages
function writeLog($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Log the start of the login attempt
    writeLog("Login attempt for username: $username", $logFile);

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Log the query execution
        writeLog("Executed query to fetch password for username: $username", $logFile);

        $hashed_password = $stmt->fetchColumn();

        // Log the result of the password fetch
        writeLog("Fetched hashed password for username: $username", $logFile);

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            // Log successful login
            writeLog("Successful login for username: $username", $logFile);
            header("Location: index.php"); // Redirect to your main page
            exit();
        } else {
            // Log failed login attempt
            writeLog("Failed login attempt for username: $username", $logFile);
            echo "Invalid username or password!";
            // Log detailed debugging information
            writeLog("Entered password: $password", $logFile);
            writeLog("Hashed password from database: $hashed_password", $logFile);
        }
    } catch (PDOException $e) {
        // Log the exception
        writeLog("PDOException: " . $e->getMessage(), $logFile);
        echo "Error: " . $e->getMessage();
    }
}

// End output buffering and flush the output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
        </form>
    </div>
</body>
</html>
