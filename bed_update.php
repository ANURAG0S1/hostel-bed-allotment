<?php include 'auth/auth.php';

// Check user type and restrict access
if (!($_SESSION['username'] == 'meadmin')) {
    echo "Access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php include 'includes/head.php'; ?>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4 p-4">
        <h2 class="mb-4">Change Bed Status</h2>



        <div id="result"></div>

        <form method="post" action="api/update_bed.php">
            <div id="data_available">

                <div class="form-group mt-4">
                    <label for="hostel_name" class="mb-1">Hostel Name:</label>
                    <select id="hostel_name" name="hostel_id" class="form-control" required></select>
                </div>

                <div class="form-group mt-4">
                    <label for="floor" class="mb-1">Floor:</label>
                    <select id="floor" name="floor" class="form-control" required></select>
                </div>

                <div class="form-group mt-4">
                    <label for="room" class="mb-1">Hostel Room Number:</label>
                    <select id="room" name="room" class="form-control" required></select>
                </div>

                <div class="form-group mt-4">
                    <label for="bed_id" class="mb-1">Bed Allotment:</label>
                    <select id="bed_id" name="bed_id" class="form-control" required></select>
                </div>

                <button id="submitbtn" type="submit" name="update" class="btn btn-primary mt-4 w-100">Confirm Room Allotment</button>
            </div>

            <div id="no_data_found" style="display: none;">
                <p class="alert alert-danger">No data found</p>
            </div>
        </form>
    </div>

    <script src="bed_update.js"></script>
</body>

</html>