<?php include 'auth/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats</title>
    <?php include 'includes/head.php'; ?>
</head>

<body> <?php include 'includes/header.php'; ?>
    <div class="container my-4 p-4">
        <h1 class="mb-4">Hostel Allocations Report</h1>
        <form id="filter-form" class="row my-4">
            <div class="form-group col-md-4">
                <select id="hostelfilter" class="form-control">
                    <option value="">All Hostels</option>
                    <!-- Dynamic hostel options will be populated here -->
                </select>
            </div>
            <div class="form-group col-md-3">
                <select id="floorfilter" class="form-control">
                    <option value="">All Floors</option>
                    <!-- Dynamic floor options will be populated here -->
                </select>
            </div>
            <button type="button" class="btn btn-primary col-md-2" onclick="filterTable()">Filter</button>
            <button type="button" class="btn btn-secondary col-md-2 mx-1" onclick="resetTable()">Reset</button>
        </form>
        <div id="tableContainer"></div>
    </div>
    <script src="scripts/stats.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>