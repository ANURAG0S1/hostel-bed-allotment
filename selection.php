<?php
include 'auth/auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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
                            $('#result').html('<p>' + response.error + '</p>');
                        } else {
                            const hostelMap = {
                                "2": "Boys Hostel",
                                "3": "Girls Hostel",
                                "4": "Another Hostel - A Block",
                                "5": "Another Hostel - A Block Boys",
                                "6": "Krishna BOYS HOSTEL",
                                "7": "MISHRA BOYS HOSTEL",
                                "1": "BALAJI BOYS HOSTEL"
                            };
                            document.getElementById("registered_name").value = response.name;
                            document.getElementById("gender").value = response.gender;
                            document.getElementById("student_id").value = response.id;

                            document.getElementById("hostel_name").value = response.hostel_id;
                            if (parseInt(response.hostel_id) != 0) {
                                document.getElementById("result").innerHTML = `
                                    <div class="d-flex-col vh-100" style="margin-top:220px;">
                                        <div class="m-auto text-center">
                                            <h1 class="fw-bold text-danger" style="font-size: 4.5rem; line-height: 1.2;">Error</h1>
                                            <h3 class="fw-bold mt-5">${hostelMap[response.hostel_id]}</h3>
                                            <h2 class="fs-5 mt-2">Already Alloted to this Student</h2>
                                        </div>
                                        <div style="margin-top:40px">
                                            <div class="text-center mt-4 mx-5">
                                                <a href="index.php" class="btn btn-primary">Hostel Allotment Status</a>
                                            </div>
                                        </div>
                                    </div>`;
                                document.getElementById("submitbtn").disabled = true;
                            } else {
                                var details = '<h3>Student Details</h3>';
                                fetchUnusedRooms();
                                $('#result').html(details);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#result').html('<p>An error occurred: ' + error + '</p>');
                    }
                });
            }

            $('#applicationForm').on('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting the traditional way

                // Get the application number from the input field
                var applicationNo = $('#application_no').val();

                // Call the function to fetch student details
                fetchStudentDetails(applicationNo);
            });

            // Check if there is an application number in the URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const applicationNo = urlParams.get('application_no');

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
        <h2 class="mb-4">Assign Hostel</h2>

        <form id="applicationForm" class="d-flex align-items-center mb-4 justify-content-between">
            <label for="application_no" class="w-20">Application No:</label>
            <input type="text" id="application_no" name="application_no" class="form-control mx-5 w-50" required>
            <button id="get_details" type="submit" class="btn btn-primary w-25">Get Details</button>
        </form>
        <div id="result"></div>
        <form method="post" action="api/assign_hostel.php">
            <div id="data_available" style="display: none;">
                <div class="form-group">
                    <label for="registered_name" class="mb-1">Registered Name:</label>
                    <input type="text" id="registered_name" name="registered_name" class="form-control" readonly>
                </div>

                <input type="hidden" id="student_id" name="student_id">

                <div class="form-group mt-4">
                    <label for="hostel_name" class="mb-1">Hostel Name:</label>
                    <select id="hostel_name" name="hostel_name" class="form-control" value="2" required>
                    </select>
                </div>
                <div class="form-group mt-4" style="display: none;">
                    <label for="gender" class="mb-1">Gender</label>
                    <select id="gender" name="gender" class="form-control" type="hidden" style="display: none;">
                        <option value="male">male</option>
                        <option value="female">female</option>
                    </select>
                </div>

                <button id="submitbtn" type="submit" name="update" class="btn btn-primary mt-4 w-100">Allot Hostel</button>
            </div>

            <div id="no_data_found" style="display: none;">
                <p class="alert alert-danger">No data found</p>
            </div>
        </form>
    </div>

    <script src="scripts/selection.js"></script>
</body>

</html>
