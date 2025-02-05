<?php
session_start();
require_once '../../php/db.php';
$volunteerID = $_SESSION['volunteerID']??0;

$query = "SELECT * FROM admin_settings";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$selectEnabled = $row['select_enabled'];

if(true){
    $query = "
    SELECT r.recipient_id, r.full_name, r.latitude, r.longitude, r.address, r.num_items
    , d.volunteer_id, d.status, r.phone, r.apt_num, r.city, r.hotel_info, v.phone as volunteer_phone
    FROM recipient r
    LEFT JOIN delivery d ON r.recipient_id = d.recipient_id
    left join volunteer v on v.volunteer_id = d.volunteer_id
    WHERE r.approved = 1 
    AND r.replied = 'Yes' 
    AND d.status != 'Completed'
    AND r.distributor_id IS NULL
    ";
    // AND (d.selected_date IS NULL OR (d.selected_date < NOW() - interval 10 minute OR d.volunteer_id = $volunteerID ))
    // AND (d.status != 'Confirmed' or d.volunteer_id = $volunteerID)
    $recipients = [];
    $result = $conn->query($query);
    
    if(!$result->num_rows){
        $query = " 
        SELECT r.recipient_id, r.full_name, r.latitude
        , r.longitude, r.address, r.num_items, d.volunteer_id 
        , d.status, r.phone, r.apt_num, r.city, r.hotel_info
        FROM recipient r
        LEFT JOIN delivery d ON r.recipient_id = d.recipient_id
        WHERE d.status = 'Confirmed' and d.volunteer_id = $volunteerID
        or d.status = 'Pending' and d.volunteer_id = $volunteerID
        ";   
        $result = $conn->query($query);
    }

    while ($row = $result->fetch_assoc()) {
        $recipients['recipients'][] = [
            'id' => $row['recipient_id'],
            'name' => $row['full_name'],
            'phone' => $row['phone'],
            'lat' => $row['latitude'],
            'lng' => $row['longitude'],
            'address' => $row['address'],
            'apt_num' => $row['apt_num'],
            'city' => $row['city'],
            'items' => $row['num_items'],
            'volunteer_id' => $row['volunteer_id'],
            'volunteer_phone' => $row['volunteer_phone'],
            'status' => $row['status'],
            'hotel_info' => $row['hotel_info']
        ];
    
    }
    $query = "select * from collect_location where active = 1";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $recipients['collectLocation'][]= [
            'lat' => $row['latitude'],
            'lng' => $row['longitude'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'pickup_time' => $row['pickup_time']
        ];
    }
    echo json_encode($recipients);
    
    $conn->close();
}
?>