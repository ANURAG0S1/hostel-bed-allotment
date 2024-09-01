<?php include 'auth/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            // Fetches student details based on application number
            function fetchStudentDetails(applicationNo) {
                $.ajax({
                    url: 'api/fetch_student_details.php',
                    type: 'POST',
                    data: {
                        application_no: applicationNo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            $('#result').html(`<p>${response.error}</p>`);
                        } else {
                            $('#registered_name').val(response.name);
                            $('#gender').val(response.gender);
                            $('#student_id').val(response.id);
                            $('#ref_id').val(applicationNo);
                            fetchUnusedRooms(response.gender);
                            $('#result').html('<h3>Student Details</h3>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#result').html(`<p>An error occurred: ${error}</p>`);
                    }
                });
            }

            // Handle form submission to fetch student details
            $('#applicationForm').on('submit', function(event) {
                event.preventDefault();

                // Trim the input value and fetch student details
                const applicationNo = $('#application_no').val().trim();
                fetchStudentDetails(applicationNo);
            });

            // Automatically fetch details if application_no is provided in the URL
            const applicationNo = new URLSearchParams(window.location.search).get('application_no');
            if (applicationNo) {
                $('#application_no').val(applicationNo);
                fetchStudentDetails(applicationNo);
            }


        });
    </script>
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-4 p-4">
        <h2 class="mb-4">Assign Room & Bed</h2>

        <form id="applicationForm" class="d-flex align-items-center mb-4 justify-content-between">
            <label for="application_no" class="w-20">Application No:</label>
            <input type="text" id="application_no" name="application_no" class="form-control mx-5 w-50" required>
            <button id="get_details" type="submit" class="btn btn-primary w-25">Get Details</button>
        </form>

        <div id="result"></div>

        <form method="post" action="api/assign_bed.php">
            <div id="data_available" style="display: none;">
                <div class="form-group">
                    <label for="registered_name" class="mb-1">Registered Name:</label>
                    <input type="text" id="registered_name" name="registered_name" class="form-control" readonly>
                </div>

                <input type="hidden" id="student_id" name="student_id">
                <input type="hidden" id="ref_id" name="ref_id">

                <div class="form-group mt-4">
                    <label for="hostel_name" class="mb-1">Hostel Name:</label>
                    <select id="hostel_name" name="hostel_id" class="form-control" required></select>
                </div>

                <div class="form-group mt-4" style="display: none;">
                    <label for="gender" class="mb-1">Gender:</label>
                    <select id="gender" name="gender" class="form-control" style="display: none;">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
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

    <script src="scripts/allotment.js"></script>
</body>

</html>