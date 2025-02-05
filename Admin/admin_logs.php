<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require('../php/db.php');
require_once 'vendor/autoload.php'; // Include Twilio PHP SDK
use Twilio\Rest\Client;

include('admin_header.php');
?>

<style>
    table {
        width: fit-content;
        /* height: 10px; */
    }
    .table-div{
        /* height: 50vh; */
        /* overflow-y: scroll; */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }      
    table, th, td {
        border: .1px solid black;
    }

    th, td {
        padding: 1px;
    }


/* General Button Style */
.general-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.general-button:hover {
    background-color: #0056b3;
}

main{
    margin: 0;
}

</style>
<main>
    <!-- buttons to make the page show either recipients or volunteers -->
    <style>
        .logs-cont{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin: 30px 0;
            gap: 40px;
        }
        .select-button-div{
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 300px ;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            flex-direction: column;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px
        }
        .general-button{
            width: 300px;
            height: 40px;
            font-size: 18px;
            cursor: pointer;
        }
        h1{
            margin: 10px 0;
        }
        .pagination{
            margin-top: 15px;
        }
    </style>
    <div class="logs-cont">
        <div class="select-button-div">
            <h1>Manage</h1>
            <a href="?for=delActive"><button class="general-button">Delivery</button></a>
            <a href="?for=pickActive"><button class="general-button">Pickup Locations</button></a>
        </div>
        <div class="select-button-div">
            <h1>Archives</h1>
            <a href="?for=rec"><button class="general-button">Recipients</button></a>
            <a href="?for=vol"><button class="general-button">Volunteers</button></a>
            <a href="?for=delArchive"><button class="general-button">Delivery</button></a>
        </div>
        <div class="select-button-div">
            <h1>Logs</h1>
            <a href="?for=admin"><button class="general-button">Admin</button></a>
            <a href="?for=smsAdmin"><button class="general-button">Admin Messages</button></a>
            <a href="?for=sms&table=rec"><button class="general-button">All Messages</button></a>
            <a href="?for=del"><button class="general-button">Delivery</button></a>
            <a href="?for=loc"><button class="general-button">Volunteer Locations</button></a>
        </div>
    </div>
    <?php if (($_GET['for'] == 'rec')): ?>

        <section class="gym-list">
            <h2>Recipient Archive Table</h2>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Proxy Name</th>
                            <th>Proxy Phone</th>
                            <th>Distributor ID</th>
                            <th>Textable</th>
                            <th>Number of Items</th>
                            <th>Address</th>
                            <th>Apt Number</th>
                            <th>Complex Name</th>
                            <th>Gate Code</th>
                            <th>City</th>
                            <th>Zip Code</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Hotel Info</th>
                            <th>Language</th>
                            <th>English Proficiency</th>
                            <th>Number of Adults</th>
                            <th>Number of Seniors</th>
                            <th>Number of Children</th>
                            <th>Approved</th>
                            <th>Replied</th>
                            <th>Replied Date</th>
                            <th>Gender</th>
                            <th>Householder Name</th>
                            <th>Date Arrived</th>
                            <th>Personal Status</th>
                            <th>Work Status</th>
                            <th>Nationality</th>
                            <th>Income</th>
                            <th>Income Period</th>
                            <th>Government Aid</th>
                            <th>Food Stamps</th>
                            <th>Health Insurance</th>
                            <th>Comment</th>
                            <th>Registration Date</th>
                            <th>Update Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM recipient_archive ORDER BY update_date DESC");
                        while ($row = $result->fetch_assoc()) {
                            $reply_date = strtotime($row['replied_date']);
                            $current_date = strtotime(date('Y-m-d'));
                            $diff_days = round(abs($current_date - $reply_date) / (60 * 60 * 24));
                            $num_total = $row['num_adults'] + $row['num_seniors'] + $row['num_children'];
                            echo "<tr>
                                <td>{$row['recipient_id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['proxy_name']}</td>
                                <td>{$row['proxy_phone']}</td>
                                <td>{$row['distributor_id']}</td>
                                <td>{$row['textable']}</td>
                                <td>{$row['num_items']}</td>
                                <td>{$row['address']}</td>
                                <td>{$row['apt_num']}</td>
                                <td>{$row['comp_name']}</td>
                                <td>{$row['gate_code']}</td>
                                <td>{$row['city']}</td>
                                <td>{$row['zip_code']}</td>
                                <td>{$row['latitude']}</td>
                                <td>{$row['longitude']}</td>
                                <td>{$row['hotel_info']}</td>
                                <td>{$row['language']}</td>
                                <td>{$row['english']}</td>
                                <td>{$row['num_adults']}</td>
                                <td>{$row['num_seniors']}</td>
                                <td>{$row['num_children']}</td>
                                <td>{$row['approved']}</td>
                                <td>{$row['replied']}</td>
                                <td>{$row['replied_date']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['householder_name']}</td>
                                <td>{$row['date_arrived']}</td>
                                <td>{$row['personal_status']}</td>
                                <td>{$row['work_status']}</td>
                                <td>{$row['nationality']}</td>
                                <td>{$row['income']}</td>
                                <td>{$row['income_per']}</td>
                                <td>{$row['gov_aid']}</td>
                                <td>{$row['food_stamps']}</td>
                                <td>{$row['health_insurance']}</td>
                                <td>{$row['comment']}</td>
                                <td>{$row['reg_date']}</td>
                                <td>{$row['update_date']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>


    <?php elseif (($_GET['for'] == 'vol')): ?>

        <section class="gym-list">
            <h2>Volunteer Archive Table</h2>
            
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Preference</th>
                            <th>Car Size</th>
                            <th>Comment</th>
                            <th>Replied</th>
                            <th>Replied Date</th>
                            <th>Reg Date</th>
                            <th>Deleted Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $result = $conn->query("SELECT * FROM volunteer_archive ORDER BY update_date DESC");
                        while ($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['volunteer_id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['preference']}</td>
                                <td>{$row['car_size']}</td>" . ($row['car_size'] == 'none' ? '' : "<td>{$row['comment']}</td>") . "
                                <td>{$row['replied']}</td>
                                <td>{$row['replied_date']}</td>
                                <td>{$row['reg_date']}</td>
                                <td>{$row['update_date']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>


    <?php elseif (($_GET['for'] == 'smsAdmin')): ?>
        <section class="gym-list">
            <h2>SMS Logs</h2>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>User Phone</th>
                            <th>Message</th>
                            <th>Sent By</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th>Recieved</th>
                            <th>Recieved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 20; // Number of rows per page
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                        $offset = ($page - 1) * $limit; // Calculate the offset
                        $result = $conn->query("SELECT * FROM sms_logs join admin on sent_by = admin_id ORDER BY sms_id DESC LIMIT $limit OFFSET $offset");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['sms_id']}</td>
                                <td>{$row['user']}</td>
                                <td>{$row['user_phone']}</td>
                                <td>{$row['sent_message']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['sent_at']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['error_message']}</td>
                                <td>{$row['recieved']}</td>
                                <td>{$row['recieved_at']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



                   <!-- Pagination -->
        <div class="pagination">
            <?php
            // Get the total number of rows
            $totalRows = $conn->query("SELECT COUNT(*) as total FROM sms_logs")->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRows / $limit);
            ?>

            <?php if ($page > 1): ?>
                <a href="?for=smsAdmin&page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?for=smsAdmin&page=<?= $i ?>" class="pagination-button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?for=smsAdmin&page=<?= $page + 1 ?>" class="pagination-button">Next</a>
            <?php endif; ?>
        </div>
        </section>


    <?php elseif (($_GET['for'] == 'sms')): ?>
        <section class="gym-list">
            <h2>SMS Logs</h2>
            <style>
                .sms-button{
                    width:fit-content;
                }
            </style>
            <div>
                <button class="general-button sms-button" style="margin:5px; <?php if($_GET['table'] == 'rec') echo 'background-color:gray'; ?>"><a href="?for=sms&table=rec" style="margin:0px; text-decoration: none; color:white;">Recipient Sent</a></button>
                <button class="general-button sms-button" style="margin:0px; <?php if($_GET['table'] == 'vol') echo 'background-color:gray'; ?>"><a href="?for=sms&table=vol" style="margin:0px; text-decoration: none; color:white;">Volunteer Sent</a></button>
            </div>
            <div>
                <button class="general-button sms-button" style="background-color:green; margin:5px; <?php if($_GET['table'] == 'recRecieved') echo 'background-color:gray'; ?>"><a href="?for=sms&table=recRecieved" style="margin:0px; text-decoration: none; color:white;">Recipient Recieved</a></button>
                <button class="general-button sms-button" style="background-color:green; margin:0px; <?php if($_GET['table'] == 'volRecieved') echo 'background-color:gray'; ?>"><a href="?for=sms&table=volRecieved" style="margin:0px; text-decoration: none; color:white;">Volunteer Recieved</a></button>
            </div>
            <div>
                <button class="general-button sms-button" style="background-color:brown; margin:5px; <?php if($_GET['table'] == 'recF') echo 'background-color:gray'; ?>"><a href="?for=sms&table=recF" style="margin:0px; text-decoration: none; color:white;">Recipient Failed</a></button>   
                <button class="general-button sms-button" style="background-color:brown; margin:0px; <?php if($_GET['table'] == 'volF') echo 'background-color:gray'; ?>"><a href="?for=sms&table=volF" style="margin:0px; text-decoration: none; color:white;">Volunteer Failed</a></button>
            </div>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>User Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Recieved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Your Twilio credentials
                        $accountSid = 'TWILIO_ACCOUNT_SID'; // Replace with your Twilio account SID
                        $authToken = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio auth token
                        
                        // Initialize the Twilio client
                        $client = new Client($accountSid, $authToken);

                        if($_GET['table'] == 'rec' || $_GET['table'] == 'recF' || $_GET['table'] == 'recRecieved') $phone = 'TWILIO_RECIPIENT_PHONE_NUMBER'; // Replace with your Twilio number
                        else $phone = 'TWILIO_VOLUNTEER_PHONE_NUMBER'; // Replace with your Twilio number
                        
                        // Fetch SMS history
                        if($_GET['table'] == 'recRecieved' || $_GET['table'] == 'volRecieved'){
                            $messages = $client->messages->read([
                                'to' => $phone, // Optional: Filter by sender
                            ]);
                        }else{
                            $messages = $client->messages->read([
                                'from' => $phone, // Optional: Filter by sender
                            ]);
                        }
                        
                        foreach ($messages as $message) {
                            if(($_GET['table'] == 'recRecieved' || $_GET['table'] == 'volRecieved')) $user = $message->from;
                            else $user = $message->to;
                            if(($_GET['table'] == 'rec' || $_GET['table'] == 'vol') && $message->status != 'delivered') continue;
                            if(($_GET['table'] == 'recF' || $_GET['table'] == 'volF') && $message->status == 'delivered') continue;
                            echo "<tr>
                                <td>{$user}</td>
                                <td>{$message->body}</td>
                                <td>{$message->status}</td>
                                <td>{$message->dateSent->format('Y-m-d H:i:s')}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </section>




    <?php elseif (($_GET['for'] == 'admin')): ?>
        <section class="gym-list">
            <h2>Admin Logs</h2>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Action Type</th>
                            <th>Table Name</th>
                            <th>Affected Row ID</th>
                            <th>Action Description</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 20; // Number of rows per page
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                        $offset = ($page - 1) * $limit; // Calculate the offset
                        $result = $conn->query("SELECT * FROM admin a join admin_logs b on a.admin_id = b.admin_id ORDER BY log_id DESC LIMIT $limit OFFSET $offset");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['log_id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['action_type']}</td>
                                <td>{$row['table_name']}</td>
                                <td>{$row['affected_row_id']}</td>
                                <td>{$row['action_description']}</td>
                                <td>{$row['timestamp']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



                   <!-- Pagination -->
        <div class="pagination">
            <?php
            // Get the total number of rows
            $totalRows = $conn->query("SELECT COUNT(*) as total FROM admin_logs")->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRows / $limit);
            ?>

            <?php if ($page > 1): ?>
                <a href="?for=admin&page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?for=admin&page=<?= $i ?>" class="pagination-button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?for=admin&page=<?= $page + 1 ?>" class="pagination-button">Next</a>
            <?php endif; ?>
        </div>
        </section>


    <?php elseif (($_GET['for'] == 'loc')): ?>
        <section class="gym-list">
            <h2>Volunteer Location Logs</h2>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>method</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 20; // Number of rows per page
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                        $offset = ($page - 1) * $limit; // Calculate the offset
                        $result = $conn->query("
                        SELECT location_id, l.volunteer_id, v.full_name, l.address, l.method, l.reg_date 
                        FROM location_logs l 
                        LEFT JOIN volunteer v ON l.volunteer_id = v.volunteer_id 
                        ORDER BY l.reg_date DESC 
                        LIMIT $limit OFFSET $offset
                    ");                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['volunteer_id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['address']}</td>
                                <td>{$row['method']}</td>
                                <td>{$row['reg_date']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



                   <!-- Pagination -->
        <div class="pagination">
            <?php
            // Get the total number of rows
            $totalRows = $conn->query("SELECT COUNT(*) as total FROM location_logs")->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRows / $limit);
            ?>

            <?php if ($page > 1): ?>
                <a href="?for=loc&page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?for=loc&page=<?= $i ?>" class="pagination-button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?for=loc&page=<?= $page + 1 ?>" class="pagination-button">Next</a>
            <?php endif; ?>
        </div>
        </section>




    <?php elseif (($_GET['for'] == 'del')): ?>
        <section class="gym-list">
            <h2>Volunteer or Admin's Delivery Logs</h2>
            <div class="table-div">
                <table>
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Volunteer Name</th>
                            <th>Recipient Name</th>
                            <th>Admin?</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 20; // Number of rows per page
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                        $offset = ($page - 1) * $limit; // Calculate the offset
                        $result = $conn->query("
                        SELECT delivery_id, d.volunteer_id, v.full_name as val, r.full_name as rec,username, d.status, d.reg_date 
                        FROM delivery_logs d 
                        LEFT JOIN volunteer v ON d.volunteer_id = v.volunteer_id 
                        LEFT JOIN recipient r ON d.recipient_id = r.recipient_id 
                        LEFT JOIN admin a ON d.admin_id = a.admin_id
                        ORDER BY d.reg_date DESC 
                        LIMIT $limit OFFSET $offset
                    ");                        
                    while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['delivery_id']}</td>
                                <td>{$row['val']}</td>
                                <td>{$row['rec']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['reg_date']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>


                   <!-- Pagination -->
        <div class="pagination">
            <?php
            // Get the total number of rows
            $totalRows = $conn->query("SELECT COUNT(*) as total FROM delivery_logs")->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRows / $limit);
            ?>

            <?php if ($page > 1): ?>
                <a href="?for=del&page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?for=del&page=<?= $i ?>" class="pagination-button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?for=del&page=<?= $page + 1 ?>" class="pagination-button">Next</a>
            <?php endif; ?>
        </div>
        </section>
        
        
        <?php elseif (($_GET['for'] == 'delActive')): ?>
            <section class="gym-list">
                <h2 style="margin:0 0 10px 0">Delivery Table</h2>
                
                <?php 
                    $query = "SELECT * FROM admin_settings";
                    $result = $conn->query($query);
                    if($result->fetch_assoc()['select_enabled']){
                        echo '
                        <p style="margin:0">Volunteer selection is currently enabled.</p>
                        <form action="php/enable_selection.php" method="post" style="margin:0">
                            <button class="general-button" style="background-color: darkred; margin:2px;">Disable Volunteer Selection</button>
                            <input type="hidden" name="edit" value="false">
                        </form>
                        ';
                    }
                    else{
                        echo '
                        <p style="margin:0">Volunteer selection is currently disabled.</p>
                        <form action="php/enable_selection.php" method="post" style="margin:2px">
                            <button class="general-button" style="background-color:">Enable Volunteer Selection</button>
                            <input type="hidden" name="edit" value="true">
                        </form>
                        ';    
                    }
                ?>



                <button id="archiveDel" class="general-button" style="background-color:red; margin:10px 0 20px 0; width:fit-content">Reset and Archive Delivery Table</button>
                <script>
                    document.getElementById('archiveDel').addEventListener('click', function() {
                        if(confirm('Are you sure you want to reset the delivery table? This will delete all the records in the table and archive the delivery table. This action cannot be undone.')){
                            window.location.href = '?for=delActive&resetDelActive=1';
                        }
                    });
                </script>
                <?php 
                    if(isset($_GET['resetDelActive']) && $_GET['resetDelActive'] == 1){
                        //archive the delivery table to delivery_archive
                        $result = $conn->query("SELECT * FROM delivery where status != 'Pending' or volunteer_id is not null ");
                        while($row = $result->fetch_assoc()){
                            $sql = "INSERT INTO delivery_archive (recipient_id, volunteer_id, status, selected_date, update_date) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("iisss", $row['recipient_id'], $row['volunteer_id'], $row['status'], $row['selected_date'], $row['update_date']);
                            $stmt->execute();
                            $stmt->close();
                        }
                        $sql = "Update delivery set status = 'Pending', selected_date = '0000-00-00 00:00:00', volunteer_id = null";
                        $conn->query($sql);
                        echo "<script>
                            window.location.href = '?for=delActive';
                        </script>";
                    }
                ?>
                <div class="table-div">
                    <table>
                        <thead>
                            <tr>
                                <th>Recipient ID</th>
                                <th>Recipient Name</th>
                                <th>Volunteer Phone</th>
                                <th>Volunteer Name</th>
                                <th>Status</th>
                                <th>Selected At</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                            SELECT d.recipient_id, v.full_name as val, r.full_name as rec, v.phone as vphone, d.status, d.selected_date, d.update_date
                            FROM delivery d 
                            LEFT JOIN volunteer v ON d.volunteer_id = v.volunteer_id 
                            LEFT JOIN recipient r ON d.recipient_id = r.recipient_id 
                            ORDER BY v.full_name DESC
                        ");                        
                        while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['recipient_id']}</td>
                                    <td>{$row['rec']}</td>
                                    <td>{$row['vphone']}</td>
                                    <td>{$row['val']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['selected_date']}</td>
                                    <td>{$row['update_date']}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>


        <?php elseif (($_GET['for'] == 'delArchive')): ?>
            <section class="gym-list">
                <h2 style="margin:0 0 10px 0">Delivery Archive Table</h2>

                <div class="table-div">
                    <table>
                        <thead>
                            <tr>
                                <th>Recipient ID</th>
                                <th>Recipient Name</th>
                                <th>Volunteer Phone</th>
                                <th>Volunteer Name</th>
                                <th>Status</th>
                                <th>Selected At</th>
                                <th>Last Updated</th>
                                <th>Archived At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $limit = 20; // Number of rows per page
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                            $offset = ($page - 1) * $limit; // Calculate the offset
                            $result = $conn->query("
                            SELECT d.archive_date,d.recipient_id, v.full_name as val, r.full_name as rec, v.phone as vphone, d.status, d.selected_date, d.update_date
                            FROM delivery_archive d 
                            LEFT JOIN volunteer v ON d.volunteer_id = v.volunteer_id 
                            LEFT JOIN recipient r ON d.recipient_id = r.recipient_id 
                            ORDER BY v.full_name DESC
                            LIMIT $limit OFFSET $offset
                        ");                        
                        while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['recipient_id']}</td>
                                    <td>{$row['rec']}</td>
                                    <td>{$row['vphone']}</td>
                                    <td>{$row['val']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['selected_date']}</td>
                                    <td>{$row['update_date']}</td>
                                    <td>{$row['archive_date']}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                                   <!-- Pagination -->
        <div class="pagination">
            <?php
            // Get the total number of rows
            $totalRows = $conn->query("SELECT COUNT(*) as total FROM delivery_archive")->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRows / $limit);
            ?>

            <?php if ($page > 1): ?>
                <a href="?for=delArchive&page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?for=delArchive&page=<?= $i ?>" class="pagination-button <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?for=delArchive&page=<?= $page + 1 ?>" class="pagination-button">Next</a>
            <?php endif; ?>
        </div>


            </section>



        <?php elseif (($_GET['for'] == 'pickActive')): ?>
            <section class="gym-list">
                <h2>Pick Up Location</h2>
                <style>
                    input{
                        font-size: small;
                    }
                </style>
                <?php
                if(isset($_GET['active']) && $_GET['active'] == 1){
                    $id = $_POST['id'];
                    $sql = "UPDATE collect_location SET active = 1 WHERE location_id = $id";
                    $conn->query($sql);
                }else if(isset($_GET['active']) && $_GET['active'] == 0){
                    $id = $_POST['id'];
                    $sql = "UPDATE collect_location SET active = 0 WHERE location_id = $id";
                    $conn->query($sql);
                }else if(isset($_GET['delete']) && $_GET['delete'] == 1){
                    $id = $_POST['id'];
                    $sql = "DELETE FROM collect_location WHERE location_id = $id";
                    $conn->query($sql);
                }else if(isset($_GET['add']) && $_GET['add'] == 1){
                    $address = $_POST['address'];
                    $latitude = $_POST['latitude'];
                    $longitude = $_POST['longitude'];
                    $pickup_time = $_POST['pickup_time'];
                    $phone = $_POST['phone'];
                    $num_items = $_POST['num_items'];
                    $sql = "INSERT INTO collect_location (address, latitude, longitude, pickup_time, phone, num_items) VALUES ('$address', $latitude, $longitude, '$pickup_time', '$phone', $num_items)";
                    $conn->query($sql);
                }
                
                ?>
                <div class="table-div">
                    <table>
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Address</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Pickup Time</th>
                                <th>Phone</th>
                                <th>Number of Boxes</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><button id="add-button" type="submit" name="add" value="1" style="background-color:green; color:white; width:fit-content; font-size:large; margin: 5px;">Add</button></td>
                                <td><input id="pickup-address" type="text" name="address" placeholder="Address" required></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <select id="pickup-time" name="pickup_time">
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                        <option value="11:30">11:30</option>
                                        <option value="12:00">12:00</option>
                                        <option value="12:30">12:30</option>
                                        <option value="13:00">13:00</option>
                                        <option value="13:30">13:30</option>
                                        <option value="14:00">14:00</option>
                                        <option value="14:30">14:30</option>
                                        <option value="15:00">15:00</option>
                                        <option value="15:30">15:30</option>
                                        <option value="16:00">16:00</option>
                                        <option value="16:30">16:30</option>
                                        <option value="17:00">17:00</option>
                                    </select>
                                </td>
                                <td><input id="pickup-phone" type="tel" name="phone" placeholder="Phone" required></td>
                                <td><input id="pickup-num" type="number" name="num_items" placeholder="Number of Boxes" required></td>
                                <td></td>

                            <script>
                                
                                document.getElementById('add-button').addEventListener('click',async function() {
                                    const address = document.getElementById('pickup-address').value;
                                    const pickupTime = document.getElementById('pickup-time').value;
                                    const phone = document.getElementById('pickup-phone').value;
                                    const numItems = document.getElementById('pickup-num').value;

                                    // Form data object
                                    const formData = new FormData();

                                    // Add address, pickup time, phone, and number of boxes to the form data
                                    formData.append('address', address);
                                    formData.append('pickup_time', pickupTime);
                                    formData.append('phone', phone);
                                    formData.append('num_items', numItems);

                                    // Fetch latitude and longitude using OpenCage Geocoding API
                                    const apiKey = 'OPEN_CAGE_API_KEY'; // Replace with your OpenCage API key
                                    const response = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(address)}&key=${apiKey}`);
                                    const data = await response.json();

                                    if (data.results.length > 0) {
                                        const lat = data.results[0].geometry.lat;
                                        const lng = data.results[0].geometry.lng;

                                        // Add latitude and longitude to the form data
                                        formData.append('latitude', lat);
                                        formData.append('longitude', lng);

                                        // Send the form data to the server
                                        const serverResponse = await fetch('admin_logs.php?for=pickActive&add=1', {
                                            method: 'POST',
                                            body: formData
                                        });

                                        if (serverResponse.ok) {
                                            alert('Data submitted successfully!');
                                            location.reload(); // Refresh the page
                                        } else {
                                            alert('Error submitting data to the server.');
                                        }
                                    } else {
                                        alert('No results found for the address.');
                                    }
                                    });
                                </script>



                            </tr>
                            <?php
                            $result = $conn->query("select * from collect_location order by active desc");
                        while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>
                                        <style>form{margin:2px;}</style>
                                        <form action='admin_logs.php?for=pickActive&active=1' method='post'>
                                            <input type='hidden' name='id' value='{$row['location_id']}'>
                                            <button type='submit' name='active' value='1'onclick=\"return confirm('Are you sure you want to Activate this location?')\">Active</button>
                                        </form>
                                        <form action='admin_logs.php?for=pickActive&active=0' method='post'>
                                            <input type='hidden' name='id' value='{$row['location_id']}'>
                                            <button type='submit' name='active' value='0' onclick=\"return confirm('Are you sure you want to Deactivate this location?')\">Inactive</button>
                                        </form>
                                        <form action='admin_logs.php?for=pickActive&delete=1' method='post'>
                                            <input type='hidden' name='id' value='{$row['location_id']}'>                                    
                                            <button type='submit' name='delete' value='1' onclick=\"return confirm('Are you sure you want to delete this location?')\">Delete</button>
                                        </form>
                                    </td>
                                    <td>{$row['address']}</td>
                                    <td>{$row['latitude']}</td>
                                    <td>{$row['longitude']}</td>
                                    <td>{$row['pickup_time']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['num_items']}</td>
                                    <td>{$row['active']}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
            

        <?php endif; ?>


</main>
<footer>
    <p>&copy; 2024 AID</p>
</footer>
