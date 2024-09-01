// Automatically fetch details if application_no is provided in the URL
window.onload = function () {
  fetchRooms();
};

var statsData;
function fetchRooms() {
  // Construct the query string based on provided parameters
  const queryParams = new URLSearchParams();

  // Construct the full URL with query parameters
  const url = `api/all_beds.php?${queryParams.toString()}`;

  // Perform the fetch request
  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      if (data) {
        let res = transformData(data);
        let roomsUpdated = updateRoomStats(res);
        statsData = roomsUpdated;
        updateTableOptions(roomsUpdated);
        generateTable(roomsUpdated);
      }
    })
    .catch((error) => console.error("Error:", error));
}

function transformData(data) {
  const nestedData = {};

  // Group data by hostel
  data.forEach((entry) => {
    const {
      hostel_name,
      floor_number,
      room_number,
      bed_id,
      bed_number,
      capacity,
      gender,
      is_occupied,
    } = entry;

    // Convert capacity to a number for comparison
    const numericCapacity = Number(capacity);

    // Check if the conversion is successful
    if (isNaN(numericCapacity)) {
      console.error(`Invalid capacity value: ${capacity}`);
      return; // Skip this entry
    }

    // Initialize nested structures if they do not exist
    if (!nestedData[hostel_name]) {
      nestedData[hostel_name] = {};
    }

    if (!nestedData[hostel_name][floor_number]) {
      nestedData[hostel_name][floor_number] = {};
    }

    if (!nestedData[hostel_name][floor_number][room_number]) {
      nestedData[hostel_name][floor_number][room_number] = [];
    }

    // Check if the bed_id already exists in the current room
    const existingBed = nestedData[hostel_name][floor_number][room_number].some(
      (bed) => bed.bed_id === bed_id
    );

    // If bed_id does not exist, push the new bed data
    if (!existingBed) {
      nestedData[hostel_name][floor_number][room_number].push({
        bed_id,
        bed_number,
        capacity: numericCapacity,
        gender,
        is_occupied,
      });
    } else {
      console.warn(
        `Bed with ID ${bed_id} already exists in room ${room_number} on floor ${floor_number} in hostel ${hostel_name}`
      );
    }
  });

  // Return the nested data object
  return nestedData;
}

function updateRoomStats(data) {
  let hostelStats = [];

  Object.keys(data).forEach((hostel_name) => {
    let hostel = data[hostel_name];
    let hostel_capacity = 0;
    let hostel_filled = 0;
    let hostel_vacant = 0;
    let floorStats = [];

    Object.keys(hostel).forEach((floor_name) => {
      let floor = hostel[floor_name];
      let floor_capacity = 0;
      let floor_filled = 0;
      let floor_vacant = 0;
      let roomStats = [];

      Object.keys(floor).forEach((room_name) => {
        let room = floor[room_name];
        let room_capacity = 0;
        let room_filled = 0;
        let room_vacant = 0;

        room.forEach((bed) => {
          room_capacity++;
          if (bed.is_occupied == 1) {
            // Use strict equality
            room_filled++;
          } else {
            room_vacant++;
          }
        });

        floor_capacity += room_capacity;
        floor_filled += room_filled;
        floor_vacant += room_vacant;

        roomStats.push({
          room_name: room_name,
          capacity: room_capacity,
          filled: room_filled,
          vacant: room_vacant,
        });
      });

      hostel_capacity += floor_capacity;
      hostel_filled += floor_filled;
      hostel_vacant += floor_vacant;

      floorStats.push({
        floor_name: floor_name,
        capacity: floor_capacity,
        filled: floor_filled,
        vacant: floor_vacant,
        rooms: roomStats,
      });
    });

    hostelStats.push({
      hostel_name: hostel_name,
      capacity: hostel_capacity,
      filled: hostel_filled,
      vacant: hostel_vacant,
      floors: floorStats,
    });
  });

  return hostelStats;
}

function generateTable(data) {
  // Function to create a table row
  function createTableRow(cells) {
    return "<tr>" + cells.map((cell) => `<td>${cell}</td>`).join("") + "</tr>";
  }

  // Function to create a nested table for rooms
  function createNestedTable(rooms) {
    let rows = "";
    for (const [roomName, roomData] of Object.entries(rooms)) {
      rows += createTableRow([
        roomData.room_name,
        roomData.capacity,
        roomData.filled,
        roomData.vacant,
      ]);
    }
    return `
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Room Number</th>
              <th>Capacity</th>
              <th>Filled</th>
              <th>Vacant</th>
            </tr>
          </thead>
          <tbody>
            ${rows}
          </tbody>
        </table>`;
  }

  // Generate the main table
  let rows = "";
  for (const [index, hostelData] of Object.entries(data)) {
    let floors = hostelData.floors;
    let floorRows = "";
    for (const [floorName, floorData] of Object.entries(floors)) {
      floorRows += createTableRow([
        hostelData.hostel_name,
        floorData.floor_name,
        floorData.capacity,
        floorData.filled,
        floorData.vacant,
        createNestedTable(floorData.rooms),
      ]);
    }
    rows += `${floorRows}`;
  }

  const tableHTML = `
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Hostel </th>
            <th>Floor </th>
            <th>Capacity</th>
            <th>Filled</th>
            <th>Vacant</th>
            <th>Rooms</th>
          </tr>
        </thead>
        <tbody>
          ${rows}
        </tbody>
      </table>`;

  // Write the generated HTML to the document
  document.getElementById("tableContainer").innerHTML = tableHTML;
}

function updateTableOptions(stats) {
  // Clear existing options
  let hostelElement = document.getElementById("hostelfilter");
  hostelElement.innerHTML = '<option value="">All Hostels</option>';

  Object.keys(stats).forEach((hostel_id) => {
    const optionElement = document.createElement("option");
    optionElement.value = hostel_id;
    optionElement.textContent = stats[hostel_id].hostel_name;
    hostelElement.appendChild(optionElement);
  });

  document.getElementById("hostelfilter").addEventListener("change", (e) => {
    if (event.target.value) {
      let floors = stats[e.target.value].floors;
      let floorElement = document.getElementById("floorfilter");
      floorElement.innerHTML = '<option value="z">All Floors</option>';

      floors.forEach((floor) => {
        const optionElement = document.createElement("option");
        optionElement.value = floor.floor_name;
        optionElement.textContent = formatFloorName(floor.floor_name);
        floorElement.appendChild(optionElement);
      });
    }
  });
}

function filterTable() {
  let hostel = $("#hostelfilter").val();
  let floor = $("#floorfilter").val();
  if (hostel == "" && floor == "") {
    generateTable(statsData);
  } else if (hostel) {
    if (floor) {
      if (floor == "z") {
        generateTable([statsData[hostel]]);
        return;
      }
      let data = statsData[hostel].floors.filter((floorItem) => {
        return floorItem.floor_name == floor;
      });
      let hostelConfig = Object.assign({}, statsData[hostel]);
      hostelConfig.floors = [...data];
      generateTable([hostelConfig]);
    } else {
      generateTable([statsData[hostel]]);
    }
  } else {
    resetTable();
  }
}

function resetTable() {
  generateTable(statsData);
}
function formatFloorName(floorCode) {
  const floorMap = {
    GF: "Ground Floor",
    FF: "First Floor",
    SF: "Second Floor",
    TF: "Third Floor",
  };
  return floorMap[floorCode] || floorCode;
}
