let unUsed = {};
let hostels = {};
let selectedHostel = {};
let selectedFloor = {};
let selectedRoom = {};

function selectOptionByValue(value, id) {
  const selectElement = document.getElementById(id);
  selectElement.value = value;
}

async function fetchUnusedRooms(gender = "", hostelName = "", roomType = "") {
  const queryParams = new URLSearchParams({
    gender,
    hostel_name: hostelName,
    room_type: roomType,
  });
  const url = `api/all_beds.php?${queryParams.toString()}`;

  try {
    const response = await fetch(url);
    const data = await response.json();

    console.log(data);

    if (data) {
      document.getElementById("data_available").style.display = "block";
      unUsed = transformData(data);
      hostels = reduceUniqueObjects(data, ["hostel_id", "hostel_name"]);
      console.log(hostels);

      displayOptions(hostels, "hostel_name");
    }
  } catch (error) {
    console.error("Error fetching unused rooms:", error);
  }
}

function reduceUniqueObjects(data, keys, criteria = {}) {
  const seen = new Set();

  return data.reduce((acc, item) => {
    // Check if the item satisfies the criteria, or if no criteria are provided
    const satisfiesCriteria =
      Object.keys(criteria).length === 0 ||
      Object.keys(criteria).every((key) => item[key] === criteria[key]);

    if (satisfiesCriteria) {
      const uniqueKey = keys.map((key) => item[key]).join("|");

      if (!seen.has(uniqueKey)) {
        seen.add(uniqueKey);
        acc.push(
          keys.reduce((obj, key) => {
            if (item.hasOwnProperty(key)) obj[key] = item[key];
            return obj;
          }, {})
        );
      }
    }

    return acc;
  }, []);
}

function transformData(data) {
  return data.reduce(
    (acc, { hostel_id, floor_number, room_number, ...bedInfo }) => {
      if (!acc[hostel_id]) acc[hostel_id] = {};
      if (!acc[hostel_id][floor_number]) acc[hostel_id][floor_number] = {};
      if (!acc[hostel_id][floor_number][room_number])
        acc[hostel_id][floor_number][room_number] = [];

      acc[hostel_id][floor_number][room_number].push(bedInfo);

      return acc;
    },
    {}
  );
}

function displayOptions(data, id) {
  const select = document.getElementById(id);
  select.style.display = "block";
  select.innerHTML = '<option value="">Select...</option>';

  if (id === "hostel_name") {
    data.forEach((hostel) => {
      const newOption = document.createElement("option");
      newOption.value = hostel.hostel_id;
      newOption.text = hostel.hostel_name;
      select.add(newOption);
    });
  } else {
    select.removeAttribute("disabled");
    const options = Object.keys(data);
    options.forEach((option) => {
      const newOption = document.createElement("option");
      newOption.value = option;
      newOption.text = id === "floor" ? formatFloorName(option) : option;
      select.add(newOption);
    });
  }
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

function populateBeds(beds) {
  console.log(beds);

  const select = document.getElementById("bed_id");
  select.innerHTML = "";
  select.removeAttribute("disabled");
  select.style.display = "block";

  beds.forEach((bed) => {
    const newOption = document.createElement("option");
    newOption.value = bed.bed_id;
    newOption.text = bed.bed_number;
    select.add(newOption);
  });

  selectOptionByValue(beds[0].bed_id, "bed_id");
}

window.onload = function () {
  fetchUnusedRooms();
  document
    .getElementById("hostel_name")
    ?.addEventListener("change", (event) => {
      resetSelection(["floor", "room", "bed_id"]);
      selectedHostel = unUsed[event.target.value];
      if (selectedHostel) displayOptions(selectedHostel, "floor");
      console.log(selectedHostel);
    });

  document.getElementById("floor")?.addEventListener("change", (event) => {
    selectedFloor = selectedHostel?.[event.target.value];
    if (selectedFloor) displayOptions(selectedFloor, "room");
    console.log(selectedFloor);
  });

  document.getElementById("room")?.addEventListener("change", (event) => {
    selectedRoom = selectedFloor?.[event.target.value];
    if (selectedRoom) populateBeds(selectedRoom);
    console.log(selectedRoom);
  });
};

function resetSelection(ids) {
  ids.forEach((id) => {
    const element = document.getElementById(id);
    if (element) {
      element.value = "";
      element.innerHTML = "";
      element.setAttribute("disabled", true);
    }
  });
}
