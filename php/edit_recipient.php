<?php
session_start();
require_once 'db.php';

error_Reporting(E_ALL);
Ini_Set('display_errors', 1);

$for = $_GET['for'];
$value = $_GET['value'];

$lat = $_GET['lat'];
$lng = $_GET['lng'];
if(isset($lat) && isset($lng)){
    $sql = "UPDATE recipient SET latitude = '$lat', longitude = '$lng' WHERE recipient_id = " . $_SESSION['recipientID'];
    $conn->query($sql);
} 

$sql = "UPDATE recipient SET $for = '$value' WHERE recipient_id = " . $_SESSION['recipientID'];

if ($conn->query($sql) === TRUE) {
    if($for == 'phone'){
        $_SESSION['recipientPhone'] = $value;
        header('Location: ../recipientpage.php');
    }else{
        session_destroy();
        header('Location: recipient_login.php?phone=' . $_SESSION['recipientPhone']);
    }
} else {
    header('Location: ../recipientpage.php?recierror=true');
}

$conn->close();
?>