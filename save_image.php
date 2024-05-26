<?php
$db_host = 'localhost:3309';
$db_user = 'root';
$db_password = '';
$db_name = 'wsn_database';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['imageData']) && isset($_POST['filename'])) {
    $imageData = $_POST['imageData'];
    $filename = $_POST['filename'];

    // Insert the image data and filename into the database
    $sql = "INSERT INTO factorylayout (factoryname, layout_image) VALUES ('$filename', '$imageData')";

    if ($conn->query($sql) === TRUE) {
        echo "Image saved successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: Image data or filename not received";
}



$conn->close();
?>
