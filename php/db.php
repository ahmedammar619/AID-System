<?php
$servername = "DB_SERVER_NAME";
$username = "DB_USERNAME";
$password = "DB_PASSWORD";
$dbname = "DB_NAME";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
