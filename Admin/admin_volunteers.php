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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['approve'])) {
    $approve = $_GET['approve'];
    $volunteer_id = $_POST['id'];

    $sql = "UPDATE volunteer SET approved = ? WHERE volunteer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $approve, $volunteer_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit'])) {
    $volunteer_id = $_POST['volunteer_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $preference = $_POST['preference'];
    $notf_preference = $_POST['notf_preference'];
    $car_size = $_POST['car_size'];
    $language = $_POST['language'] ?? null;
    $replied = $_POST['replied'] == '' ? null : $_POST['replied'];
    $zip_code = $_POST['zip_code'] ?? null;
    
    // i want to update volunteer's replied date only if the user changed the replied by value
    $sql = "select replied from volunteer where volunteer_id = $volunteer_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($row['replied'] != $replied){
        $sql = "UPDATE volunteer SET replied_date = now() WHERE volunteer_id = $volunteer_id";
        $conn->query($sql);
    }
    
    $sql = "UPDATE volunteer SET  phone = ?, email = ?, preference = ?, notf_preference = ?, car_size = ?, language = ?, replied = ?, zip_code = ? WHERE volunteer_id = $volunteer_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $phone, $email, $preference, $notf_preference, $car_size, $language, $replied, $zip_code);
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




include('admin_header.php');
?>

<main>
    <section class="gym-list">
    <?php if (isset($_GET['id'])): ?>
        <!-- center the form with style -->
        <form class="edit-recipient-form" action="admin_volunteers.php?edit=true" method="post">
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
                                <th>Zip Code</th>
                                <th>Preference</th>
                                <th>Car Size</th>
                                <th>Notification Preference</th>
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
                                <td><input type='text' name='zip_code' value='{$row['zip_code']}'></td>
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
                                <td><input type='text' name='notf_preference' value='{$row['notf_preference']}'></td>
                                <td>
                                    <select name='replied' id='replied'>
                                        <option value='No response'>No response</option>
                                        <option value='Delivery'>Delivery</option>
                                        <option value='Packing'>Packing</option>
                                        <option value='Both'>Both</option>
                                        <option value='Next month'>Next month</option>
                                        <option value='Delete from list'>Delete from list</option>
                                        <option value='Picked up'>Picked up</option>
                                        <option value='Delivered'>Delivered</option>
                                    </select>
                                </td>
                                <td><select name='language' id='language'>
                                    <option value='English'>English</option>
                                    <option value='Arabic'>Arabic</option>
                                    <option value='Farsi'>Farsi</option>
                                    <option value='Spanish'>Spanish</option>
                                    <option value='Urdu'>Urdu</option>
                                    <option value='Myanmar'>Burmese</option>
                                    <option value='Pashto'>Pashto</option>
                                    <option value='Other'>Other</option>
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

        <?php else: ?>

        <h1 style="margin:9px; font-size: 40px;">Volunteer Data</h1>

        <section id="sms-section">
            <h3 style="margin:0px; color:rgb(0, 123, 255)">Send SMS to Volunteers Panel</h3>
            <h6 style="margin:0px">Send SMS to the selected volunteers, by checking the boxes of the volunteers you want to select. Then type or select template to send!<br> Note: The arabic might seem left sided but will be sent perfectly fine.</h6>

            <div>
                <label for="sms-template">Choose an SMS template:</label>
                <select id="sms-template" onchange="loadTemplate()">
                    <option value="">Select a template</option>
                    <?php
                    $templates = $conn->query("SELECT * FROM sms_templates where table_name = 'volunteer' order by template_id desc limit 10");
                    while ($template = $templates->fetch_assoc()) {
                        echo "<option value='".htmlspecialchars($template['template_text'], ENT_QUOTES)."'>".substr($template['template_text'], 0, 30)."...</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="custom-sms">Or write a custom message:</label>
                <textarea id="custom-sms" rows="4" cols="50"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <input type="checkbox" id="save-template" name="save-template">
                <label for="save-template">Save this custom message as a new template</label>
            </div>

            <button class="general-button" onclick="confirmAndSendSMS()">Send SMS</button>
        </section> 

        <h2 id="volunteer-count" style="margin: 15px 0 0 0;">Number of Volunteers: <span style="color:red" id="volunteer-count-value">0</span></h2>
        <h2 style="margin-top: 0px;">Selected: <span id="selected-count" style="color:red">0</span></h2>


        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                    <th>Actions</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Zip Code</th>
                    <th>Preference</th>
                    <th>Car Size</th>
                    <th>Notification Preference</th>
                    <th>Replied</th>
                    <th>Language</th>
                    <th>Approved</th>
                </tr>
            </thead>
            <tbody>
            <tr style="background-color: darkgrey;">
                    <td></td>
                    <td>
                        <button type="button" class="general-button" style="width: 100%; margin-bottom: 10px; background-color: #f44336;" onclick="clearSearch()">Clear</button>
                        <button type="button" class="general-button" style="width: 100%;" onclick="filterTable()">Search</button>
                    </td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="ID"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="Name"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="Phone"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="Email"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="Zip Code"></td>
                    <td>
                        <div>
                            <label><input type='checkbox' onclick="filterTable()" name='preference[]' value='none'>none</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='preference[]' value='Reminder every month'>Reminder every month</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='preference[]' value='Committed every month'>Committed every month</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='preference[]' value='Committed one time'>Committed one time</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='preference[]' value='none'>none</label>
                        </div>
                    </td>
                    <td>
                        <div>
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='none'>none</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='6'>6</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='8'>8</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='12'>12</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='18'>18</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='car_size[]' value='48'>48</label>
                        </div>
                    </td>

                    <td>
                        <div>
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Food Pantry Packing'>Food Pantry Packing</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Food Pantry Delivery'>Food Pantry Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Iftar Preparation'>Iftar Preparation</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Iftar Delivery'>Iftar Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Ramadan Basket Delivery'>Ramadan Basket Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Gift Wrapping'>Gift Wrapping</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Gift Delivery'>Gift Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Bazaar Preparation'>Bazaar Preparation</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Bazaar Volunteering'>Bazaar Volunteering</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Adha Meat Delivery'>Adha Meat Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Furniture Pickup & Delivery'>Furniture Pickup & Delivery</label>
                            <hr width="100%">
                            <label><input type='checkbox' onclick="filterTable()" name='notf_preference[]' value='Any Other Events'>Any Other Events</label>
                        </div>
                    </td>

                    <td><div>
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='No response'>No response</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Delivery'>Delivery</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Packing'>Packing</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Both'>Both</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Next month'>Next month</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Delete from list'>Delete from list</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Picked up'>Picked up</label>
                        <hr width="100%">
                        <label><input type='checkbox' onclick="filterTable()" name='replied[]' value='Delivered'>Delivered</label>
                    </div></td>
                    <td><div style="display: flex; flex-direction: column;">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="English"> English</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Arabic"> Arabic</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Farsi"> Farsi</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Spanish"> Spanish</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Urdu"> Urdu</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Myanmar"> Myanmar</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Pashto"> Pashto</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="language[]" value="Other"> Other</label>
                    </div>
                    <td> <label><input type="checkbox" onclick="filterTable()" name="approve" value="1"> Approved</label></td>
                </tr>


                <?php
                $result = $conn->query("SELECT * FROM volunteer ORDER BY volunteer_id DESC");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td><input type='checkbox' class='rowCheckbox' value='{$row['volunteer_id']}'></td>
                        <td>
                            <form action='admin_volunteers.php?id={$row['volunteer_id']}' method='post' style='display:inline;'>
                                <button type='submit' name='edit'>Edit</button>
                            </form>
                            <form action='admin_volunteers.php?approve=1' method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['volunteer_id']}'>
                                <button type='submit' name='approve' value='1'>Approve</button>
                            </form>
                            <form action='admin_volunteers.php?approve=0' method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['volunteer_id']}'>
                                <button type='submit' name='approve' value='0'>Disapprove</button>
                            </form>
                            <form action='admin_volunteers.php' method='post' style='display:inline;'>
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
                        <td>{$row['notf_preference']}</td>
                        <td>{$row['replied']}</td>
                        <td>{$row['language']}</td>
                        <td>{$row['approved']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
 
    </section>
</main>


<script>
// Filter table based on search inputs

document.addEventListener('DOMContentLoaded', function() {
    filterTable(); // Initial call to populate the visibility and count
});

function filterTable() {
    const table = document.querySelector('table tbody');
    const textFilters = document.querySelectorAll('.search');
    const langCheckboxes = document.querySelectorAll('input[name="language[]"]:checked');
    const replyCheckboxes = document.querySelectorAll('input[name="replied[]"]:checked');
    const preferenceCheckboxes = document.querySelectorAll('input[name="preference[]"]:checked');
    const carSizeCheckboxes = document.querySelectorAll('input[name="car_size[]"]:checked');
    const notfPreferenceCheckboxes = document.querySelectorAll('input[name="notf_preference[]"]:checked');
    const approveCheckbox = document.querySelector('input[name="approve"]:checked');

    const rows = Array.from(table.querySelectorAll('tr')).slice(1);
    let count = 0;

    rows.forEach(row => {
        let isMatch = true;

        // Text filter
        textFilters.forEach((filter, index) => {
            const cell = row.getElementsByTagName('td')[index + 2]; // Adjust offset
            if (cell && filter.value && !cell.textContent.toLowerCase().includes(filter.value.toLowerCase())) {
                isMatch = false;
            }
        });

        // Preference checkbox filtering
        const preference = row.getElementsByTagName('td')[7]?.textContent; // Adjust for column index
        if (preferenceCheckboxes.length > 0 && !Array.from(preferenceCheckboxes).some(chk => preference.includes(chk.value))) {
            isMatch = false;
        }

        // Car Size checkbox filtering
        const carSize = row.getElementsByTagName('td')[8]?.textContent; // Adjust for column index
        if (carSizeCheckboxes.length > 0 && !Array.from(carSizeCheckboxes).some(chk => carSize.includes(chk.value))) {
            isMatch = false;
        }

        // Notification Preference checkbox filtering
        const notfPreference = row.getElementsByTagName('td')[9]?.textContent; // Adjust for column index
        if (notfPreferenceCheckboxes.length > 0 && !Array.from(notfPreferenceCheckboxes).some(chk => notfPreference.includes(chk.value))) {
            isMatch = false;
        }

        // Replied checkbox filtering
        const replied = row.getElementsByTagName('td')[10]?.textContent; // Adjust for column index
        if (replyCheckboxes.length > 0 && !Array.from(replyCheckboxes).some(chk => replied.includes(chk.value))) {
            isMatch = false;
        }

        // Language checkbox filtering
        const lang = row.getElementsByTagName('td')[11]?.textContent; // Adjust for column index
        if (langCheckboxes.length > 0 && !Array.from(langCheckboxes).some(chk => lang.includes(chk.value))) {
            isMatch = false;
        }

        // Approve checkbox filtering
        const approved = row.getElementsByTagName('td')[12]?.textContent; // Adjust for column index
        if (approveCheckbox && approved !== '1') { // Assuming '1' indicates 'Approved'
            isMatch = false;
        }

        if (isMatch) count++;
        row.style.display = isMatch ? '' : 'none';
    });

    document.getElementById('volunteer-count-value').innerHTML = count;
}
// Select/Deselect all checkboxes
function toggleSelectAll(source) {
    document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
        if (checkbox.closest('tr').style.display !== 'none') {
            checkbox.checked = source.checked;
        }
    });
    updateSelectedCount(); // Update count after select all/deselect all
}

function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked').length;
    document.getElementById('selected-count').textContent = selectedCheckboxes;
}

// Add event listeners to each row checkbox
document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});


// Clear search inputs and reset table filtering
function clearSearch() {
    document.querySelectorAll('.search').forEach(input => input.value = '');
    // document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => checkbox.checked = false);
    filterTable();
}


// for sms section:



function loadTemplate() {
    const template = document.getElementById('sms-template').value;
    if (template) {
        document.getElementById('custom-sms').value = template;
    }
}



function confirmAndSendSMS() {
    const message = document.getElementById('custom-sms').value;
    const selectedVolunteers = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
        .map(checkbox => checkbox.value);
    const saveTemplate = document.getElementById('save-template').checked;

    if (!message) {
        alert("Please enter a message.");
        return;
    }

    if (selectedVolunteers.length === 0) {
        alert("Please select at least one volunteer.");
        return;
    }

    if (confirm(`Are you sure you want to send the following message to ${selectedVolunteers.length} volunteers?\n\n${message}`)) {
        sendSMS(selectedVolunteers, message, saveTemplate);
    }
}

function sendSMS(volunteers, message, saveTemplate) {
    console.log(volunteers);
    console.log(message);
    console.log(saveTemplate);
    fetch('php/send_sms_volunteers.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({volunteers, message, saveTemplate})
    })
    .then(response => {
        console.log('Response:', response); // Log the response
        return response.json();
    })
    .then(data => {
        console.log('Data:', data); // Log the data
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send SMS.');
    });
}



</script>






<footer>
    <p>&copy; 2024 AID</p>
</footer>