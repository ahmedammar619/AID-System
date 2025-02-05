<?php
session_start();
require_once '../../php/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$phone = $_POST['phone'];
//clean phone from spaces, + and -, and if it contained 1 in the beginning, remove it and remove ( )
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
    $_SESSION['volunteerPhone'] = $row['phone'];
    $_SESSION['collected'] = $row['replied'] == 'Picked up' ? 1: 0;
    

    $sql = "insert into admin_logs  (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'CREATE', 'delivery', $volunteerID, 'Assigning volunteer to delivery')";
    $conn->query($sql);

    // Redirect to the delivery page
    header('Location: ../admin_delivery.php');
}else if($phone == 0){
    $_SESSION['volunteerID'] = 0;
    $_SESSION['volunteerName'] = 'Not Selected';
    $_SESSION['volunteerPhone'] = 'Not Selected';
    $_SESSION['collected'] = 0;
    // If the volunteer does not exist, redirect to the login page with an error message
    header('Location: ../admin_delivery.php?error=reset');
}
else{
    // If the volunteer does not exist, redirect to the login page with an error message
    header('Location: ../admin_delivery.php?error=true');
}