<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;

session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require('../php/db.php');

// Ensure the user is an admin
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

//make the volunteer version of the recipient page

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit'])) {
    $volunteer_id = $_POST['volunteer_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $preference = $_POST['preference'];
    $car_size = $_POST['car_size'];
    $language = $_POST['language'] ?? null;
    $replied = $_POST['replied'] == '' ? null : $_POST['replied'];
    // i want to update recipient's replied date only if the user changed the replied by value
    $sql = "select replied from volunteer where volunteer_id = $volunteer_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($row['replied'] != $replied){
        $sql = "UPDATE volunteer SET replied_date = now() WHERE volunteer_id = $volunteer_id";
        $conn->query($sql);
    }
    
    $sql = "UPDATE volunteer SET  phone = ?, email = ?, preference = ?, car_size = ?, language = ?, replied = ? WHERE volunteer_id = $volunteer_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $phone, $email, $preference, $car_size, $language, $replied);
    $stmt->execute();
    $stmt->close();

    $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'UPDATE', 'volunteer', $volunteer_id, 'edited volunteer in system page')";
    $conn->query($sql);
}

//delete volunteer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_volunteer'])) {
    $volunteer_id = $_POST['volunteer_id'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Copy the volunteer's data to the archive table
        $copy_sql = "INSERT INTO volunteer_archive (volunteer_id, full_name, phone, email, preference, car_size, comment, approved, replied, replied_date, reg_date, update_date, language, zip_code, notf_preference)
                     SELECT volunteer_id, full_name, phone, email, preference, car_size, comment, approved, replied, replied_date, reg_date, update_date, language, zip_code, notf_preference
                     FROM volunteer WHERE volunteer_id = ?";
        $stmt = $conn->prepare($copy_sql);
        $stmt->bind_param("i", $volunteer_id);
        $stmt->execute();
        $stmt->close();

        // Set volunteer_id to NULL in delivery table for this volunteer
        $nullify_deliveries_sql = "UPDATE delivery SET volunteer_id = NULL WHERE volunteer_id = ?";
        $stmt = $conn->prepare($nullify_deliveries_sql);
        $stmt->bind_param("i", $volunteer_id);
        $stmt->execute();
        $stmt->close();

        // Delete the volunteer from the main table
        $delete_sql = "DELETE FROM volunteer WHERE volunteer_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $volunteer_id);
        $stmt->execute();
        $stmt->close();

        $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'DELETE', 'volunteer', $volunteer_id, 'deleted volunteer from system page')";
        $conn->query($sql);

        // Commit the transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "Failed to delete volunteer: " . $e->getMessage();
    }
}

//download the excel file
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function exportToExcel($conn) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Fetch data from the database, excluding the Action column
    $query = "SELECT volunteer_id, full_name, phone, email,notf_preference, preference, car_size, comment, replied, replied_date, reg_date, update_date
              FROM volunteer ORDER BY volunteer_id DESC";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $rowIndex = 1; // Excel rows start at 1
        
        // Set the header
        $sheet->setCellValue('A'.$rowIndex, 'ID');
        $sheet->setCellValue('B'.$rowIndex, 'Name');
        $sheet->setCellValue('C'.$rowIndex, 'Phone');
        $sheet->setCellValue('D'.$rowIndex, 'Email');
        $sheet->setCellValue('E'.$rowIndex, 'Preference');
        $sheet->setCellValue('F'.$rowIndex, 'Car Size');
        $sheet->setCellValue('G'.$rowIndex, 'Comment');
        $sheet->setCellValue('H'.$rowIndex, 'Notification preference');
        $sheet->setCellValue('I'.$rowIndex, 'Replied');
        $sheet->setCellValue('J'.$rowIndex, 'Replied Date');
        $sheet->setCellValue('K'.$rowIndex, 'Language');
        $sheet->setCellValue('L'.$rowIndex, 'Reg Date');
        $sheet->setCellValue('M'.$rowIndex, 'Update Date');

        // Fetch and write each row of data
        while ($row = $result->fetch_assoc()) {
            $rowIndex++;
            $sheet->setCellValue('A'.$rowIndex, $row['volunteer_id']);
            $sheet->setCellValue('B'.$rowIndex, $row['full_name']);
            $sheet->setCellValue('C'.$rowIndex, $row['phone']);
            $sheet->setCellValue('D'.$rowIndex, $row['email']);
            $sheet->setCellValue('E'.$rowIndex, $row['preference']);
            $sheet->setCellValue('F'.$rowIndex, $row['car_size']);
            $sheet->setCellValue('G'.$rowIndex, $row['comment']);
            $sheet->setCellValue('H'.$rowIndex, $row['notf_preference']);
            $sheet->setCellValue('I'.$rowIndex, $row['replied']);
            $sheet->setCellValue('J'.$rowIndex, $row['replied_date']);
            $sheet->setCellValue('K'.$rowIndex, $row['language']);
            $sheet->setCellValue('L'.$rowIndex, $row['reg_date']);
            $sheet->setCellValue('M'.$rowIndex, $row['update_date']);
        }
    }
    
    // Output to a file
    $writer = new Xlsx($spreadsheet);
    $fileName = "volunteer_data.xlsx";
    
    // Set headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    header('Cache-Control: max-age=0');
    
    // Save the file to output
    $writer->save('php://output');

    // Log the action
    $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) 
            VALUES ('$_SESSION[admin_id]', 'READ', 'volunteer', NULL, 'downloaded excel file')";
    $conn->query($sql);

    exit;
}


if (isset($_POST['download_excel'])) {
    exportToExcel($conn);
}


include('admin_header.php');
?>

<main>
    <section class="gym-list">
    <?php if (isset($_GET['id'])): ?>
        <!-- center the form with style -->
        <form class="edit-recipient-form" action="admin_volunteers_excel.php?edit=true" method="post">
            <input type="hidden" name="volunteer_id" value="<?php echo $_GET['id']; ?>">

            <h2>Volunteer Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Comment</th>
                        <th>Replied Date</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM volunteer WHERE volunteer_id = {$_GET['id']}");
                    $row = $result->fetch_assoc();
                    echo "<tr>
                        <td>{$row['volunteer_id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['comment']}</td>
                        <td>{$row['replied_date']}</td>
                        <td>{$row['approved']}</td>
                    </tr>";
                    ?>
                    </tbody>
                    </table>
                    <table>
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Preference</th>
                                <th>Car Size</th>
                                <th>Replied</th>
                                <th>Language</th>
                                <th>Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            echo "<tr>
                                <td><input type='text' name='phone' value='{$row['phone']}'></td>
                                <td><input type='text' name='email' value='{$row['email']}'></td>
                                <td><select name='preference' id='preference'>
                                    <option value='Reminder every month'>Reminder every month</option>
                                    <option value='Committed every month'>Committed every month</option>
                                    <option value='Committed one time'>Committed one time</option>
                                    <option value='none'>none</option>
                                </select></td>
                                <td><select name='car_size' id='car_size'>
                                    <option value='6'>6</option>
                                    <option value='8'>8</option>
                                    <option value='12'>12</option>
                                    <option value='18'>18</option>
                                    <option value='48'>48</option>
                                    <option value='none'>none</option>
                                </select></td>
                                <td>
                                    <select name='replied' id='replied'>
                                        <option value='No Response'>No Response</option>
                                        <option value='Delivery'>Delivery</option>
                                        <option value='Packing'>Packing</option>
                                        <option value='Both'>Both</option>
                                        <option value='Next month'>Next month</option>
                                        <option value='Delete from list'>Delete from list</option>
                                    </select>
                                </td>
                                <td><select name='language' id='language'>
                                    <option value='English'>English</option>
                                    <option value='Arabic'>Arabic</option>
                                </select></td>
                                <td><input type='text' name='approved' value='{$row['approved']}'></td>
                                <script>
                                    document.getElementById('language').value = '{$row['language']}';
                                    document.getElementById('car_size').value = '{$row['car_size']}';
                                    document.getElementById('preference').value = '{$row['preference']}';
                                    document.getElementById('replied').value = '{$row['replied']}';
                                </script>
                                </tr>";

                            
                            ?>
                    </table>
                    <button class="general-button" type="submit" name="update_volunteer">Update</button>
        </form>

    <?php endif; ?>

    <h1 style="margin:10px 0 0 0 ; font-size: 30px;">Volunteer Data</h1>
    <form action="admin_volunteers_excel.php" method="post">
        <button class="general-button" type="submit" name="download_excel" style="min-width: 300px; font-size: large; margin-bottom:15px">Download Excel</button>
    </form>


        <table>
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Zip Code</th>
                    <th>Preference</th>
                    <th>Car Size</th>
                    <th>Replied</th>
                    <th>Replied Date</th>
                    <th>Notification Preference</th>
                    <th>Approved</th>
                    <th>Comment</th>
                    <th>Language</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM volunteer ORDER BY volunteer_id DESC");
                while ($row = $result->fetch_assoc()) {
                    $reply_date = strtotime($row['replied_date']);
                    $current_date = strtotime(date('Y-m-d'));
                    $diff_days = round(abs($current_date - $reply_date) / (60 * 60 * 24));
                    echo "<tr>
                        <td>
                            <form action='admin_volunteers_excel.php?id={$row['volunteer_id']}' method='post' style='display:inline;'>
                                <button type='submit' name='edit'>Edit</button>
                            </form>
                            <form action='admin_volunteers_excel.php' method='post' style='display:inline;'>
                                <input type='hidden' name='volunteer_id' value='{$row['volunteer_id']}'>
                                <button type='submit' name='delete_volunteer' onclick='return confirm(\"Are you sure you want to delete {$row['full_name']} and add them to the archive?\")'>Delete</button>
                            </form>
                        </td>
                        <td>{$row['volunteer_id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['zip_code']}</td>
                        <td>{$row['preference']}</td>
                        <td>{$row['car_size']}</td>
                        <td>{$row['replied']}</td>
                        <td>{$row['replied_date']}</td>
                        <td>{$row['notf_preference']}</td>
                        <td>{$row['approved']}</td>
                        <td>{$row['comment']}</td>
                        <td>{$row['language']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <script>
        // Save scroll position before the page is unloaded or hidden
        const saveScrollPosition = () => {
            sessionStorage.setItem('scrollX', window.scrollX); // Horizontal scroll position
            sessionStorage.setItem('scrollY', window.scrollY); // Vertical scroll position
        };

        window.addEventListener('pagehide', saveScrollPosition);
        window.addEventListener('beforeunload', saveScrollPosition);

        // Restore scroll positions after the page is shown or loaded
        const restoreScrollPosition = () => {
            const scrollX = sessionStorage.getItem('scrollX');
            const scrollY = sessionStorage.getItem('scrollY');

            if (scrollX !== null && scrollY !== null) {
                // Add a small delay to ensure the page is fully loaded
                setTimeout(() => {
                    window.scrollTo(parseInt(scrollX), parseInt(scrollY)); // Restore both positions
                    sessionStorage.removeItem('scrollX'); // Clear stored horizontal position
                    sessionStorage.removeItem('scrollY'); // Clear stored vertical position
                }, 100); // Adjust the delay as needed
            }
        };

        window.addEventListener('pageshow', restoreScrollPosition);
        window.addEventListener('load', restoreScrollPosition);
    </script>


</main>
<footer>
    <p>&copy; 2024 AID</p>
</footer>