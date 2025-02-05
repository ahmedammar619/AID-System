<?php
session_start();
require_once 'db.php';

$phone = $_POST['phone'] ?? $_GET['phone'];
//clean phone from spaces, + and -, and if it contained 1 in the beginning, remove it
$phone = preg_replace(['/^\+1|^1/', '/\)/', '/\(/', '/\s+/', '/\+/', '/-/', '/\s1/'], '', $phone);
// Check if the volunteer exists
$query = "SELECT * FROM recipient where phone = '$phone'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // If the volunteer exists, get their ID
    $row = $result->fetch_assoc();
    $recipientID = $row['recipient_id'];

    // Set the session variable
    $_SESSION['recipientID'] = $recipientID;
    $_SESSION['recipientName'] = $row['full_name'];
    $_SESSION['recipientEmail'] = $row['email'];
    $_SESSION['recipientPhone'] = $row['phone'];
    $_SESSION['recipientAddress'] = $row['address'];
    $_SESSION['recipientHotelInfo'] = $row['hotel_info'];
    $_SESSION['recipientAptNum'] = $row['apt_num'];
    $_SESSION['recipientCity'] = $row['city'];
    $_SESSION['recipientZipCode'] = $row['zip_code'];
    $_SESSION['recipientGateCode'] = $row['gate_code'];
    $_SESSION['recipientComplexName'] = $row['comp_name'];
    

    // Redirect to the delivery page
    header('Location: ../recipientpage.php');
}
else{
    // Redirect to the login page with an error message
    header('Location: ../recipientpage.php?error=true');
}