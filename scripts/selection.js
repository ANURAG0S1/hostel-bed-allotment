const hostelMap = {
  "Boys Hostel": 2,
  "Girls Hostel": 3,
  "Another Hostel - A Block": 4,
  "Another Hostel - A Block Boys": 5,
  "Krishna BOYS HOSTEL": 6,
  "MISHRA BOYS HOSTEL": 7,
  "BALAJI BOYS HOSTEL": 1,
};
function selectOptionByValue(value, id) {
 
  const selectElement = document.getElementById(id);
  selectElement.value = value;
}

function fetchUnusedRooms(gender = "", hostelName = "", roomType = "") {
  // Construct the query string based on provided parameters
  const queryParams = new URLSearchParams();

  if (gender) queryParams.append("gender", gender);
  if (hostelName) queryParams.append("hostel_name", hostelName);
  if (roomType) queryParams.append("room_type", roomType);

  // Construct the full URL with query parameters
  const url = `api/un_alloted_beds.php?${queryParams.toString()}`;

  // Perform the fetch request
  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      if (data) {
        document.getElementById("data_available").style.display = "block";
        let res = transformData(data);
        unUsed = res;
        displayOptions(res, "hostel_name");
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

    if (!nestedData[hostel_name]) {
      nestedData[hostel_name] = {};
    }

    if (!nestedData[hostel_name][floor_number]) {
      nestedData[hostel_name][floor_number] = {};
    }

    if (!nestedData[hostel_name][floor_number][room_number]) {
      nestedData[hostel_name][floor_number][room_number] = [];
    }

    nestedData[hostel_name][floor_number][room_number].push({
      bed_id,
      bed_number,
      capacity,
      gender,
      is_occupied,
    });
  });

  // Convert the nested data to a JSON string with pretty print
  return nestedData;
}

// Function to display the results (customize this as needed)
function displayOptions(data, id) {
  const select = document.getElementById(id);
  select.style.display = "block";
  let firstIteration = true;
  let options = Object.keys(data);
  if (options.length) select.innerHTML = "";

  for (const [option, value] of Object.entries(data)) {
    if (firstIteration) {
      const newOption = document.createElement("option");
      newOption.value = "";
      newOption.text = "";
      select.add(newOption);
      firstIteration = false;
    }
    const newOption = document.createElement("option");
    newOption.value = hostelMap[option];
    newOption.text = option;
    select.add(newOption);
  }
}
