<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bed Assignment Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Assign Bed to Student</h2>
    <form id="bedAssignmentForm">
        <!-- Hostel Selection -->
        <div class="form-group">
            <label for="hostel_id">Select Hostel:</label>
            <select class="form-control" id="hostel_id" name="hostel_id" required>
                <option value="">--Select Hostel--</option>
                <!-- Hostel options will be loaded here by PHP -->
                <?php
                include 'config.php';
                $hostelQuery = "SELECT id, name AS hostel_name FROM hostels";
                $hostelStmt = $pdo->prepare($hostelQuery);
                $hostelStmt->execute();
                $hostels = $hostelStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($hostels as $hostel) {
                    echo '<option value="' . htmlspecialchars($hostel['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($hostel['hostel_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- Floor Selection -->
        <div class="form-group">
            <label for="floor_number">Select Floor:</label>
            <select class="form-control" id="floor_number" name="floor_number" required>
                <option value="">--Select Floor--</option>
                <!-- Floor options will be loaded dynamically -->
            </select>
        </div>

        <!-- Room Selection -->
        <div class="form-group">
            <label for="room_id">Select Room:</label>
            <select class="form-control" id="room_id" name="room_id" required>
                <option value="">--Select Room--</option>
                <!-- Room options will be loaded dynamically -->
            </select>
        </div>

        <!-- Bed Selection -->
        <div class="form-group">
            <label for="bed_id">Select Bed:</label>
            <select class="form-control" id="bed_id" name="bed_id" required>
                <option value="">--Select Bed--</option>
                <!-- Bed options will be loaded dynamically -->
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Assign Bed</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    // Load floors based on selected hostel
    $('#hostel_id').change(function() {
        var hostelId = $(this).val();
        if (hostelId) {
            $.ajax({
                url: 'api/fetch_floors.php',
                type: 'POST',
                data: {hostel_id: hostelId},
                success: function(data) {
                  
                    $('#floor_number').html(data);
                    $('#room_id').html('<option value="">--Select Room--</option>');
                    $('#bed_id').html('<option value="">--Select Bed--</option>');
                }
            });
        } else {
            $('#floor_number').html('<option value="">--Select Floor--</option>');
            $('#room_id').html('<option value="">--Select Room--</option>');
            $('#bed_id').html('<option value="">--Select Bed--</option>');
        }
    });

    // Load rooms based on selected hostel and floor
    $('#floor_number').change(function() {
        var hostelId = $('#hostel_id').val();
        var floorNumber = $(this).val();
        if (hostelId && floorNumber) {
            $.ajax({
                url: 'api/fetch_rooms.php',
                type: 'POST',
                data: {hostel_id: hostelId, floor_number: floorNumber},
                success: function(data) {
                    console.log(data)
                    $('#room_id').html(data);
                    $('#bed_id').html('<option value="">--Select Bed--</option>');
                }
            });
        } else {
            $('#room_id').html('<option value="">--Select Room--</option>');
            $('#bed_id').html('<option value="">--Select Bed--</option>');
        }
    });

    // Load beds based on selected hostel, floor, and room
    $('#room_id').change(function() {
        var roomId = $(this).val();
        if (roomId) {
            $.ajax({
                url: 'api/fetch_beds.php',
                type: 'POST',
                data: {room_id: roomId},
                success: function(data) {
                    $('#bed_id').html(data);
                }
            });
        } else {
            $('#bed_id').html('<option value="">--Select Bed--</option>');
        }
    });
});
</script>
</body>
</html>
