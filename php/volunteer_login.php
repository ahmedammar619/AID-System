<?php
session_start();
require_once 'db.php';

$phone = $_POST['phone'];
//clean phone from spaces, + and -, and if it contained 1 in the beginning, remove it
$phone = preg_replace(['/^\+1|^1/', '/\)/', '/\(/', '/\s+/', '/\+/', '/-/', '/\s1/'], '', $phone);
// Check if the volunteer exists
$query = "SELECT * FROM volunteer WHERE phone = '$phone'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // If the volunteer exists, get their ID
    $row = $result->fetch_assoc();
    $volunteerID = $row['volunteer_id'];

    // Set the session variable
    $_SESSION['volunteerID'] = $volunteerID;
    $_SESSION['volunteerName'] = $row['full_name'];
    $_SESSION['collected'] = $row['replied'] == 'Picked up' ? 1: 0;
    


    // Redirect to the delivery page
    header('Location: ../deliver.php');
}else{
    // If the volunteer does not exist, redirect to the login page with an error message
    header('Location: ../deliver.php?error=true');
}