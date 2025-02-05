<?php
session_start();
require_once 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$volunteer_id = $_POST['volunteer_id'];
$address = $_POST['address'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$method = $_POST['method'];


$sql = "INSERT INTO location_logs (volunteer_id, address, latitude, longitude, method) VALUES ('$volunteer_id', '$address', $latitude, $longitude, '$method')";
$conn->query($sql);



header('Location: ../deliver.php');
?>