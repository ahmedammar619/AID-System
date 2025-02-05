<?php
session_start();
require_once '../../php/db.php';
// $_SESSION['volunteerID'] = 5;
$volunteerID = $_SESSION['volunteerID'];
$recipient_id = $_GET['recipient_id'];
$for = $_GET['for'];
if($volunteerID == 0) header('Location: ../admin_delivery.php?error=true');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$forEdited = '';
if($for == 'select' || $for == 'selectAll') $forEdited = 'Selected';
else if($for == 'pickup') $forEdited = 'Picked up';
else if($for == 'confirm' || $for == 'confirmAll') $forEdited = 'Confirmed';
else if($for == 'completed') $forEdited = 'Completed';
else if($for == 'deselect' || $for == 'deleteAll') $forEdited = 'Deselected';


if($for == 'confirmAll' || $for == 'deleteAll' || $for == 'selectAll'){
    //insert delivery log for each recipient
    $recipients = $_GET['recipients'];
    $recipients = explode(',', $recipients);
    $recipients = array_map('intval', $recipients);
    foreach($recipients as $recipient){
        $sql = "insert into delivery_logs (volunteer_id, recipient_id, admin_id, status) values ($volunteerID, $recipient, $_SESSION[admin_id], '$forEdited')";
        $conn->query($sql);
    }}
else{
    $sql = "insert into delivery_logs (volunteer_id, recipient_id, admin_id, status) values ($volunteerID, $recipient_id, $_SESSION[admin_id], '$forEdited')";
    $conn->query($sql);
}

if(isset($_SESSION['volunteerID'])){

    if($for == 'select'){
        //check first if it was already selected or not
        $sql = "Update delivery set selected_date = NOW(), volunteer_id = $volunteerID where (recipient_id = $recipient_id and (selected_date IS NULL OR selected_date < NOW() - interval 10 minute or volunteer_id is null)) and (status = 'Pending' or status= 'Failed') ";
        $conn->query($sql);
        
    }
    else if($for == 'deselect'){
        $null = NULL;
        $sql = "Update delivery set selected_date = ?, volunteer_id = ?, status = 'Pending' where recipient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $null, $null, $recipient_id);
        $stmt->execute();
        $stmt->close();
    }
    else if($for == 'selectAll'){
        $recipients = $_GET['recipients'];
        $recipients = explode(',', $recipients);
        $recipients = array_map('intval', $recipients);
        $recipients = implode(',', $recipients);
        $sql = "Update delivery set selected_date = NOW(), volunteer_id = $volunteerID where recipient_id in ($recipients) and (selected_date IS NULL OR selected_date < NOW() - interval 10 minute) and (status = 'Pending' or status= 'Failed')";
        $conn->query($sql);
    }
    else if($for == 'confirm'){
        $sql = "Update delivery set status = 'Confirmed' where recipient_id = $recipient_id";
        $conn->query($sql);
    }
    else if($for == 'confirmAll'){
        $recipients = $_GET['recipients'];
        $recipients = explode(',', $recipients);
        $recipients = array_map('intval', $recipients);
        $recipients = implode(',', $recipients);
        $sql = "Update delivery set status = 'Confirmed' where recipient_id in ($recipients)";
        $conn->query($sql);
    }
    else if($for == 'deleteAll'){
        $recipients = $_GET['recipients'];
        $recipients = explode(',', $recipients);
        $recipients = array_map('intval', $recipients);
        $recipients = implode(',', $recipients);
        $sql = "Update delivery set status = 'Pending', selected_date = null, volunteer_id = null where recipient_id in ($recipients)";
        $conn->query($sql);
    }
    else if($for == 'completed'){
        $sql = "Update delivery set status = 'Completed' where recipient_id = $recipient_id";
        $conn->query($sql);

        // i wanna make sure that every single box is delivered! and if so then update the volunteer status to delivered
        $sql = "select status from delivery where volunteer_id = $volunteerID";
        $result = $conn->query($sql);
        $notDone = 0;
        while($row = $result->fetch_assoc()){
            if($row['status'] != 'Completed') $notDone = 1;
        };
        if($notDone == 0){
            $sql = "Update volunteer set replied = 'Delivered' where volunteer_id = $volunteerID";
            $conn->query($sql);
        }
    }
    else if($for == 'pickup'){
        $sql = "Update volunteer set replied = 'Picked up' where volunteer_id = $volunteerID";
        $conn->query($sql);
        $_SESSION['collected'] = 1;
    }
    
    header('Location: ../admin_delivery.php');
}