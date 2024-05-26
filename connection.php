<?php
$db_host = 'localhost:3309';
$db_email = 'root'; // Replace 'root' with your actual email
$db_password = ''; // Replace 'your_password_here' with the actual password
$db_name = 'wsn_database'; // give your database name here

$conn = mysqli_connect($db_host, $db_email, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
