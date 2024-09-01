<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check user type and restrict access
if (!($_SESSION['username'] == 'meadmin')) {
    echo "Access denied.";
    exit();
}
