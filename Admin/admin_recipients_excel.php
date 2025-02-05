<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require('../php/db.php');


// edit recipient
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit'])) {
    $recipient_id = $_POST['recipient_id'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $proxy_name = $_POST['proxy_name'];
    $proxy_phone = $_POST['proxy_phone'];
    $address = $_POST['address'];
    $apt_num = $_POST['apt_num'];
    $city = $_POST['city'];
    $zip_code = $_POST['zip_code'];
    $hotel_info = $_POST['hotel_info'];
    $language = $_POST['language'] == '' ? null : $_POST['language'];
    $nationality = $_POST['nationality'] == '' ? null : $_POST['nationality'];
    $income = $_POST['income'];
    $income_per = $_POST['income_per'] == '' ? null : $_POST['income_per'];
    $num_adults = $_POST['num_adults'];
    $num_seniors = $_POST['num_seniors'];
    $num_children = $_POST['num_children'];
    $gov_aid = $_POST['gov_aid'];
    
    $sql = "UPDATE recipient join recipient_details on recipient.recipient_id = recipient_details.recipient_id SET full_name = ?, phone = ?, email = ?, proxy_name = ?, proxy_phone = ?, address = ?, apt_num = ?, city = ?, zip_code = ?, hotel_info = ?, language = ?, nationality = ?, income = ?, income_per = ?, num_adults = ?, num_seniors = ?, num_children = ?, gov_aid = ? WHERE recipient.recipient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssisiiisi",
        $full_name, $phone, $email, $proxy_name, $proxy_phone, $address, $apt_num, $city, $zip_code, $hotel_info, $language, $nationality, $income, $income_per, $num_adults, $num_seniors, $num_children, $gov_aid, $recipient_id
    );
    $stmt->execute();
    $stmt->close();
    $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'UPDATE', 'recipient', $recipient_id, 'edited recipient in excel page')";
    $conn->query($sql);
}


//to download the excel file
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// Function to download data as Excel
function downloadAsExcel($conn, $query, $filename = 'data.xlsx') {
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Fetch and write headers
        $fields = $result->fetch_fields();
        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field->name;
        }
        $sheet->fromArray($headers, null, 'A1');

        // Fetch and write data rows
        $rowNumber = 2;
        while ($row = $result->fetch_assoc()) {
            $sheet->fromArray($row, null, 'A' . $rowNumber);
            $rowNumber++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // exit;
    } else {
        echo "No data found for " . $filename;
    }
}




if (isset($_POST['download_excel'])) {
    if ($_GET['download_excel'] == 1) {
        downloadAsExcel($conn, "SELECT * FROM recipient_archive ORDER BY update_date DESC", 'recipient_archive_data.xlsx');
        exit;
    }
    if ($_GET['download_excel'] == 2) {
        downloadAsExcel($conn, 
        " SELECT r.recipient_id, r.full_name, r.phone, r.email, r.distributor_id, r.textable, r.num_items, r.address, r.apt_num, r.comp_name, r.gate_code, r.city, r.zip_code, r.latitude, r.longitude, r.hotel_info, r.language, r.english, r.num_adults, r.num_seniors, r.num_children, r.approved, r.replied, r.replied_date, r.update_date, rd.gender, rd.householder_name, rd.date_arrived, rd.proxy_name, rd.proxy_phone, rd.age, rd.country, rd.personal_status, rd.work_status, rd.nationality, rd.income, rd.spouse_name, rd.spouse_age, rd.spouse_work, rd.income_per, rd.gov_aid, rd.food_stamps, rd.health_insurance, rd.comment, rd.reg_date 
        FROM recipient r JOIN recipient_details rd ON r.recipient_id = rd.recipient_id;
        ", 'recipient_data.xlsx');
    }
    if ($_GET['download_excel'] == 3) {
        downloadAsExcel($conn, "       
                SELECT 
                    r.full_name AS recipient_name,
                    r.phone AS recipient_phone,
                    c.name AS child_name,
                    c.gender AS child_gender,
                    c.age AS child_age,
                    c.school_status AS school_status,
                    c.job_status AS job_status,
                    c.has_disability AS has_disability
                FROM 
                    recipient_children c
                JOIN 
                    recipient r ON c.recipient_id = r.recipient_id;
        ", "children.xlsx");
        exit;
    }
}

// delete recipient
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_recipient'])) {
    $recipient_id = $_POST['recipient_id'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Copy the recipient's data (joined from recipient and recipient_details) to the archive table
        $copy_sql = "
            INSERT INTO recipient_archive (
                recipient_id, full_name, phone, email, proxy_name, proxy_phone, distributor_id, textable, num_items, 
                address, apt_num, city, zip_code, latitude, longitude, hotel_info, language, english, num_adults, 
                num_seniors, num_children, approved, replied, replied_date, gender, householder_name, date_arrived, 
                personal_status, work_status, nationality, income, income_per, gov_aid, food_stamps, health_insurance, 
                comment, reg_date, update_date, comp_name, gate_code, spouse_name, spouse_work, spouse_age
            )
            SELECT 
                r.recipient_id, r.full_name, r.phone, r.email, rd.proxy_name, rd.proxy_phone, r.distributor_id, r.textable, 
                r.num_items, r.address, r.apt_num, r.city, r.zip_code, r.latitude, r.longitude, r.hotel_info, r.language, 
                r.english, r.num_adults, r.num_seniors, r.num_children, r.approved, r.replied, r.replied_date, 
                rd.gender, rd.householder_name, rd.date_arrived, rd.personal_status, rd.work_status, rd.nationality, 
                rd.income, rd.income_per, rd.gov_aid, rd.food_stamps, rd.health_insurance, rd.comment, rd.reg_date, 
                r.update_date, r.comp_name, r.gate_code, rd.spouse_name, rd.spouse_work, rd.spouse_age
            FROM 
                recipient r
            JOIN 
                recipient_details rd ON r.recipient_id = rd.recipient_id
            WHERE 
                r.recipient_id = ?;
        ";
        $stmt = $conn->prepare($copy_sql);
        $stmt->bind_param("i", $recipient_id);
        $stmt->execute();
        $success = mysqli_stmt_affected_rows($stmt);
        $stmt->close();

        if($success > 0){
            // Delete delivery rows associated with this recipient
            $delete_deliveries_sql = "DELETE FROM delivery WHERE recipient_id = ?";
            $stmt = $conn->prepare($delete_deliveries_sql);
            $stmt->bind_param("i", $recipient_id);
            $stmt->execute();
            $stmt->close();

            // Delete the recipient from the recipient_details table
            $delete_details_sql = "DELETE FROM recipient_details WHERE recipient_id = ?";
            $stmt = $conn->prepare($delete_details_sql);
            $stmt->bind_param("i", $recipient_id);
            $stmt->execute();
            $stmt->close();

            // Delete the recipient from the main recipient table
            $delete_sql = "DELETE FROM recipient WHERE recipient_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $recipient_id);
            $stmt->execute();
            $stmt->close();

            // Log the deletion action in the admin_logs table
            $log_sql = "
                INSERT INTO admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) 
                VALUES (?, 'DELETE', 'recipient', ?, 'deleted recipient from system page')
            ";
            $stmt = $conn->prepare($log_sql);
            $stmt->bind_param("ii", $_SESSION['admin_id'], $recipient_id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "Failed to delete recipient: " . $e->getMessage();
    }
}


include('admin_header.php');
?>

<main>
    <section class="gym-list">
        <?php if (isset($_GET['id'])): ?>
            <!-- center the form with style -->
            <form class="edit-recipient-form" action="admin_recipients_excel.php?edit=true" method="post">
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    
                <h2>Recipient Details</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM recipient WHERE recipient_id = {$_GET['id']}");
                        $row = $result->fetch_assoc();
                        echo "<tr>
                            <td>{$row['recipient_id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['reg_date']}</td>
                        </tr>";
                        ?>
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Proxy Name</th>
                            <th>Proxy Phone</th>
                            <th>Address</th>
                            <th>Apt Num</th>
                            <th>City</th>
                            <th>Zip Code</th>
                            <th>Hotel Info</th>
                            <th>Language</th>
                            <th>Nationality</th>
                            <th>Income</th>
                            <th>Income Per</th>
                            <th>Adults</th>
                            <th>Seniors</th>
                            <th>Children</th>
                            <th>Gov Aid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo "
                        <input type='hidden' name='recipient_id' value='{$row['recipient_id']}'>
                        <tr>
                            <td><input type='text' name='full_name' value='{$row['full_name']}'></td>
                            <td><input type='text' name='phone' value='{$row['phone']}'></td>
                            <td><input type='text' name='email' value='{$row['email']}'></td>
                            <td><input type='text' name='proxy_name' value='{$row['proxy_name']}'></td>
                            <td><input type='text' name='proxy_phone' value='{$row['proxy_phone']}'></td>
                            <td><input type='text' name='address' value='{$row['address']}'></td>
                            <td><input type='text' name='apt_num' value='{$row['apt_num']}'></td>
                            <td><input type='text' name='city' value='{$row['city']}'></td>
                            <td><input type='text' name='zip_code' value='{$row['zip_code']}'></td>
                            <td><input type='text' name='hotel_info' value='{$row['hotel_info']}'></td>
                            <td>
                            <select id='language' name='language'>
                                <option value='English'>English</option>
                                <option value='Arabic'>Arabic</option>
                                <option value='Farsi'>Farsi</option>
                                <option value='Spanish'>Spanish</option>
                                <option value='Urdu'>Urdu</option>
                                <option value='Myanmar'>Burmese</option>
                                <option value='Pashto'>Pashto</option>
                            </select>
                            </td>
                            <td>
                            <select id='nationality' name='nationality'>
                                <option value='Other'>Other</option>
                                <option value='American Indian or Alaska Native'>American Indian or Alaska Native</option>
                                <option value='Asian'>Asian</option>
                                <option value='Black or African American'>Black or African American</option>
                                <option value='Hispanic or Latino'>Hispanic or Latino</option>
                                <option value='Native Hawaiian or Other Pacific Islander'>Native Hawaiian or Other Pacific Islander</option>
                                <option value='White'>White</option>
                            </select>
                            </td>
                            <td><input type='text' name='income' value='{$row['income']}'></td>
                            <td>
                            <select id='income_per' name='income_per'>
                                <option value='No Income'>No Income</option>
                                <option value='Per Week'>Per Week</option>
                                <option value='Per Month'>Per Month</option>
                                <option value='Per Year'>Per Year</option>
                            </select>
                            </td>
                            <td><input type='text' name='num_adults' value='{$row['num_adults']}'></td>
                            <td><input type='text' name='num_seniors' value='{$row['num_seniors']}'></td>
                            <td><input type='text' name='num_children' value='{$row['num_children']}'></td>
                            <td><input type='text' name='gov_aid' value='{$row['gov_aid']}'></td>
                            <script>
                                document.getElementById('language').value = '{$row['language']}';
                                document.getElementById('nationality').value = '{$row['nationality']}';
                                document.getElementById('income_per').value = '{$row['income_per']}';
                            </script>
                        </tr>";
                        ?>
                    </tbody>
                </table>
                <button class="general-button" type="submit" name="update_recipient">Update</button>
            </form>
        <hr style="width: 100%;">
            <?php endif; ?>


            <h1 style="font-size: 30px; margin:10px 0 0 0;">Recipient Data</h1>
            <form action="admin_recipients_excel.php?download_excel=2" method="post" style="margin:5px">
                <button class="general-button" type="submit" name="download_excel" style="min-width: 300px; font-size: large; margin:0px">Download Recipient Excel</button>
            </form>
            <form action="admin_recipients_excel.php?download_excel=1" method="post" style="margin: 0px">
                <button class="general-button" type="submit" name="download_excel" style="min-width: 300px; font-size: large; margin:0px">Download Recipient Archive Excel</button>
            </form>
            <form action="admin_recipients_excel.php?download_excel=3" method="post" style="margin:5px">
                <button class="general-button" type="submit" name="download_excel" style="min-width: 300px; font-size: large; margin:0px">Download Children Excel</button>
            </form>
        <table class="recipient-excel-table">
            <thead>
                <tr>
                    <th>Actions</th>
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
                    <th>Country</th>
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
                    <th>Spouse Name</th>
                    <th>Spouse Work Status</th>
                    <th>Spouse Age</th>
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

                $result = $conn->query("SELECT *  FROM recipient join recipient_details on recipient.recipient_id = recipient_details.recipient_id ORDER BY recipient.recipient_id DESC");
                while ($row = $result->fetch_assoc()) {
                    $reply_date = strtotime($row['replied_date']);
                    $current_date = strtotime(date('Y-m-d'));
                    $total = $row['num_adults'] + $row['num_seniors'] + $row['num_children'];
                    $diff_days = round(abs($current_date - $reply_date) / (60 * 60 * 24));
                    echo "<tr>
                        <td>
                            <form action='admin_recipients_excel.php?id={$row['recipient_id']}' method='post' style='display:inline;'>
                                <button type='submit' name='edit'>Edit</button>
                            </form>
                            <form action='admin_recipients_excel.php' method='post' style='display:inline;'>
                                <input type='hidden' name='recipient_id' value='{$row['recipient_id']}'>
                                <button type='submit' name='delete_recipient' onclick='return confirm(\"Are you sure you want to delete {$row['full_name']} and add them to the archive?\")'>Delete</button>
                            </form>
                        </td>
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
                        <td>{$row['country']}</td>
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
                        <td>{$row['spouse_name']}</td>
                        <td>{$row['spouse_work']}</td>
                        <td>{$row['spouse_age']}</td>
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
    </section>


</main>
<footer>
    <p>&copy; 2024 AID</p>
</footer>
