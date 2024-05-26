<?php
// Establish database connection (replace with your own credentials)
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

// Fetch maximum ID from the database
$sql = "SELECT MAX(id) AS max_id FROM network_devices";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $max_id = $row["max_id"];
    echo json_encode($max_id);
} else {
    // If no rows are returned, assume the maximum ID is 0
    echo json_encode(0);
}

// Close database connection
$conn->close();
?>
