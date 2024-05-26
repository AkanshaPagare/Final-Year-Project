<?php
// Database credentials
$servername = "localhost:3309";
$username = "root";
$password = "";
$dbname = "wsn_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the POST data
$temperature = isset($_POST["temperature"]) ? $_POST["temperature"] : null;
$humidity = isset($_POST["humidity"]) ? $_POST["humidity"] : null;
$macAddress = isset($_POST["mac_address"]) ? $_POST["mac_address"] : null;

// Insert data into table only if all fields are provided
if ($temperature !== null && $humidity !== null && $macAddress !== null) {
  // Prepare SQL statement to insert data into table
  $sql = "INSERT INTO sensor_data (temperature, humidity, mac_address) VALUES ('$temperature', '$humidity', '$macAddress')";

  if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
} else {
  echo "Error: Missing data";
}

$conn->close();
?>
