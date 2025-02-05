<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require('../php/db.php');

// Ensure the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['approve'])) {
    $approve = $_GET['approve'];
    $recipient_id = $_POST['id'];

    $sql = "UPDATE recipient SET approved = ? WHERE recipient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $approve, $recipient_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit'])) {
    $dis_id = $_POST['distributor_id'] == '' ? null : $_POST['distributor_id'];
    $recipient_id = $_POST['recipient_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $apt_num = $_POST['apt_num'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $language = $_POST['language'] ?? null;
    $num_items = $_POST['num_items'];
    $replied = $_POST['replied'];
    $english = $_POST['english'];

    if($dis_id != null) {
        $sql = "update recipient set num_items = ((select sum(num_items) from recipient where distributor_id = $dis_id)+1) where recipient_id = $dis_id";
        $conn->query($sql);
    }

    // i want to update recipient's replied date only if the user changed the replied by value
    $sql = "select replied from recipient where recipient_id = $recipient_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($row['replied'] != $replied){
        $sql = "UPDATE recipient SET replied_date = now() WHERE recipient_id = $recipient_id";
        $conn->query($sql);
    }
    
    $sql = "update recipient set distributor_id = ?,phone=?,email=?, address = ?, city = ?, latitude = ?, longitude = ?, language = ?, num_items = ?, replied = ?, english=?, apt_num=? where recipient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssddsisssi", $dis_id,$phone,$email, $address, $city, $latitude, $longitude, $language, $num_items, $replied,$english, $apt_num, $recipient_id);
    $stmt->execute();
    $stmt->close();

    $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'UPDATE', 'recipient', $recipient_id, 'edited recipient in system page')";
    $conn->query($sql);
    
}

include('admin_header.php');
?>

<main>
    
    <section class="gym-list">


        <h1 style="margin:9px; font-size: 40px;">Recipient Data</h1>

        <section id="sms-section">
        <h3 style="margin:0px; color:rgb(0, 123, 255)">Send SMS to Recipients Panel</h3>
        <h6 style="margin:0px">Send SMS to the selected recipients, by checking the boxes of the recipients you want to select. Then type or select template to send!<br> Note: The arabic might seem left sided but will be sent perfectly fine.</h6>

        <div>
            <label for="sms-template">Choose an SMS template:</label>
            <select id="sms-template" onchange="loadTemplate()">
                <option value="">Select a template</option>
                <?php
                $templates = $conn->query("SELECT * FROM sms_templates where table_name = 'recipient' order by template_id desc limit 10");
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
        <h2 id="recipient-count" style="margin: 15px 0 0 0;">Number of Recipients: <span style="color:red" id="recipient-count-value">0</span></h2>
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
                    <th>Distributer ID</th>
                    <th>Items</th>
                    <th>Apt Num</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Zip Code</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>English</th>
                    <th>Language</th>
                    <th>Replied Date</th>
                    <th>Replied With</th>
                    <th>Approved</th>
                </tr>
            </thead>
            <tbody>
                <!-- search bar for each row -->
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
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="ID"></td>
                    <td><input type="checkbox" id="sortCheckbox" onchange="filterTable()">Sort</td>
                    <td></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="Address"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="City"></td>
                    <td><input type="search" class="search" onkeydown="if (event.key === 'Enter') { filterTable(); }" placeholder="ZIP Code"></td>
                    <td></td>
                    <td></td>
                    <td> 
                        <label><input type="checkbox" onclick="filterTable()" name="english[]" value="Fluent">Fluent</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()" name="english[]" value="Intermediate">Intermediate</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()" name="english[]" value="Basic">Basic</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()" name="english[]" value="None">None</label>
                    </td>
                    <td>
                    <div style="display: flex; flex-direction: column;">
                        <!-- <strong>Language:</strong> -->
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
                    </div>
                </td>
                <td></td>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <!-- <strong>Language:</strong> -->
                        <label><input type="checkbox" onclick="filterTable()"name="replied[]" value="Yes"> Yes</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="replied[]" value="Owns a car"> Owns a car</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="replied[]" value="Next month"> Next month</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="replied[]" value="Delete from list"> Delete from list</label>
                        <hr width="100%">
                        <label><input type="checkbox" onclick="filterTable()"name="replied[]" value="No response"> No response</label>
                    </div>
                </td>
                <td> 
                    <label><input type="checkbox" onclick="filterTable()" name="approve" value="1"> Approved</label>
                    <hr width="100%">
                    <label><input type="checkbox" onclick="filterTable()" name="approve" value="0"> Not Approved</label>
                </td>
            </tr>
            <?php
                $result = $conn->query("SELECT * FROM recipient ORDER BY recipient_id DESC");
                while ($row = $result->fetch_assoc()) {
                    $reply_date = strtotime($row['replied_date']);
                    $current_date = strtotime(date('Y-m-d'));
                    $diff_days = round(abs($current_date - $reply_date) / (60 * 60 * 24));
                    echo "
                    <tr>
                        <td><input type='checkbox' class='rowCheckbox' value='{$row['recipient_id']}'></td>
                        <td id='edit-{$row['recipient_id']}'>
                            <form action='admin_recipients.php?id={$row['recipient_id']}' method='post' style='display:inline;'>
                                <button type='submit' name='edit'>Edit</button>
                            </form>
                            <form action='admin_recipients.php?approve=1' method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['recipient_id']}'>
                                <button type='submit' name='approve' value='1'>Approve</button>
                            </form>
                            <form action='admin_recipients.php?approve=0' method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['recipient_id']}'>
                                <button type='submit' name='approve' value='0'>Disapprove</button>
                            </form>
                            <form action='admin_recipients_excel.php?' method='post' style='display:inline;'>
                                <input type='hidden' name='recipient_id' value='{$row['recipient_id']}'>
                                <button type='submit' name='delete_recipient' onclick='return confirm(\"Are you sure you want to delete {$row['full_name']} and add them to the archive? You will be directed to the excel page!\")'>Delete</button>
                            </form>
                        </td>
                        <td>{$row['recipient_id']}</td>
                        <td>{$row['full_name']}</td>
                        <td id='phone-{$row['recipient_id']}'>{$row['phone']}</td>
                        <td id='email-{$row['recipient_id']}'>{$row['email']}</td>
                        <td id='dist-{$row['recipient_id']}'>{$row['distributor_id']}</td>
                        <td id='items-{$row['recipient_id']}'>{$row['num_items']}</td>
                        <td id='apt-{$row['recipient_id']}'>{$row['apt_num']}</td>
                        <td id='address-{$row['recipient_id']}'>{$row['address']}</td>
                        <td id='city-{$row['recipient_id']}'>{$row['city']}</td>
                        <td id='zip-{$row['recipient_id']}'>{$row['zip_code']}</td>
                        <td>{$row['latitude']}</td>
                        <td>{$row['longitude']}</td>
                        <td id='english-{$row['recipient_id']}'>{$row['english']}</td>
                        <td id='lang-{$row['recipient_id']}'>{$row['language']}</td>
                        <td>{$diff_days} Days ago</td>
                        <td id='replied-{$row['recipient_id']}'>{$row['replied']}</td>
                        <td>{$row['approved']}</td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </section>

</main>
             <!-- edit recipient -->
    <?php if (isset($_GET['id'])): ?>
            <script>
                let lat;
                let lng;
                async function convertAddressToCoordinates(userInput) {
                    const apiKey = 'MAPBOX_API_KEY'; // Replace with your Mapbox API key
                    await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${userInput}&key=${apiKey}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.results.length > 0) {                
                                // Use the latitude and longitude of the first result
                                lat = data.results[0].geometry.lat;
                                lng = data.results[0].geometry.lng;
                                const coords = {'lat':lat, 'lng':lng};
                                // console.log(coords);
                                return coords;
                            }else{
                                return 'failed';
                            }
                        });
                }

                updateRecipient();
                function updateRecipient() {
                    const id = <?php echo $_GET['id']; ?>;
                    const actionBtn = document.getElementById(`edit-${id}`);
                    const phone = document.getElementById(`phone-${id}`);
                    const email = document.getElementById(`email-${id}`);
                    const dist = document.getElementById(`dist-${id}`);
                    const items = document.getElementById(`items-${id}`);
                    const apt = document.getElementById(`apt-${id}`);
                    const address = document.getElementById(`address-${id}`);
                    const city = document.getElementById(`city-${id}`);
                    const zip = document.getElementById(`zip-${id}`);
                    const lang = document.getElementById(`lang-${id}`);
                    const english = document.getElementById(`english-${id}`);
                    const replied = document.getElementById(`replied-${id}`);

                    const updateBtn = document.createElement('button');
                    updateBtn.classList.add('general-button');
                    updateBtn.innerText = 'Update';
                    updateBtn.addEventListener('click', async function() {
                        // Submit the form with post method to url admin_recipients.php?edit=true
                        // and handle the response
                        const formData = new FormData();
                        formData.append('recipient_id', id);
                        formData.append('phone', document.getElementById(`inp-phone-${id}`).value);
                        formData.append('email', document.getElementById(`inp-email-${id}`).value);
                        formData.append('distributor_id', document.getElementById(`inp-dist-${id}`).value);
                        formData.append('num_items', document.getElementById(`inp-items-${id}`).value);
                        formData.append('apt_num', document.getElementById(`inp-apt-${id}`).value);
                        const addressInput = document.getElementById(`inp-address-${id}`);
                        const cityInput = document.getElementById(`inp-city-${id}`);
                        formData.append('address', addressInput.value);
                        formData.append('city', cityInput.value);
                        formData.append('zip_code', document.getElementById(`inp-zip-${id}`).value);
                        formData.append('language', document.getElementById(`language`).value);
                        formData.append('english', document.getElementById(`english`).value);
                        formData.append('replied', document.getElementById(`replied`).value);
                        if (addressInput && cityInput) {
                            const city = cityInput.value;
                            const address = addressInput.value;
                            userInput = address + ', ' + city;
                            await convertAddressToCoordinates(userInput)
                            formData.append('latitude', lat);
                            formData.append('longitude', lng);
                        }
                        formData.append('latitude', lat);
                        formData.append('longitude', lng);
                        fetch('admin_recipients.php?edit=true', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.href = 'admin_recipients.php';
                            } else {
                                alert('Error submitting data to the server.');
                            }
                        });

                    });
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            updateBtn.click();
                        }
                    });
                    const exitBtn = document.createElement('button');
                    exitBtn.classList.add('general-button');
                    exitBtn.innerText = 'Exit';
                    exitBtn.style.backgroundColor = 'red';
                    exitBtn.style.marginTop = '5px';
                    exitBtn.addEventListener('click', function() {
                        window.location.href = 'admin_recipients.php';
                    });
                    actionBtn.innerHTML = '';
                    actionBtn.appendChild(updateBtn);
                    actionBtn.appendChild(exitBtn);

                    phone.innerHTML = '<input id="inp-phone-'+id+'" type="text" name="phone" value="'+phone.textContent+'">';
                    email.innerHTML = '<input id="inp-email-'+id+'" type="text" name="email" value="'+email.textContent+'">';
                    dist.innerHTML = '<input id="inp-dist-'+id+'" type="text" name="distributor_id" value="'+dist.textContent+'">';
                    items.innerHTML = '<input id="inp-items-'+id+'" type="text" name="num_items" value="'+items.textContent+'">';
                    apt.innerHTML = '<input id="inp-apt-'+id+'" type="text" name="apt_num" value="'+apt.textContent+'">';
                    address.innerHTML = '<input id="inp-address-'+id+'" type="text" name="address" value="'+address.textContent+'">';
                    city.innerHTML = '<input id="inp-city-'+id+'" type="text" name="city" value="'+city.textContent+'">';
                    zip.innerHTML = '<input id="inp-zip-'+id+'" type="text" name="zip_code" value="'+zip.textContent+'">';

                    const langValue = lang.textContent;
                    lang.innerHTML = `
                    <select name="language" id='language'>
                        <option value="English">English</option>
                        <option value="Arabic">Arabic</option>
                        <option value="Farsi">Farsi</option>
                        <option value="Spanish">Spanish</option>
                        <option value="Urdu">Urdu</option>
                        <option value="Myanmar">Burmese</option>
                        <option value="Pashto">Pashto</option>
                        <option value="Other">Other</option>
                    </select>
                    `;
                    document.querySelector('#language').value = langValue;

                    const englishValue = english.textContent;
                    english.innerHTML = `
                    <select name='english' id='english'>
                        <option value='Fluent'>Fluent</option>
                        <option value='Intermediate'>Intermediate</option>
                        <option value='Basic'>Basic</option>
                        <option value='None'>None</option>
                    </select>
                    `;
                    document.querySelector('#english').value = englishValue;

                    const repliedValue = replied.textContent;
                    replied.innerHTML = `
                    <select name='replied' id='replied'>
                        <option value='No response'>No response</option>
                        <option value='Yes'>Yes</option>
                        <option value='Owns a car'>Owns a car</option>
                        <option value='Next month'>Next month</option>
                        <option value='Delete from list'>Delete from list</option>
                    </select>
                    `;
                    document.querySelector('#replied').value = repliedValue;
                }
            </script>
    <?php endif; ?>

    <script>
        // Filter table based on search inputs

        document.addEventListener('DOMContentLoaded', function() {
            restoreFilterState(); // Initial call to populate the visibility and count
        });

        function filterTable() {
            const table = document.querySelector('table tbody');
            const textFilters = document.querySelectorAll('.search');
            const engCheckboxes = document.querySelectorAll('input[name="english[]"]:checked');
            const langCheckboxes = document.querySelectorAll('input[name="language[]"]:checked');
            const replyCheckboxes = document.querySelectorAll('input[name="replied[]"]:checked');
            const approveCheckbox = document.querySelector('input[name="approve"][value="1"]:checked');
            const notApproveCheckbox = document.querySelector('input[name="approve"][value="0"]:checked');
            const sortCheckbox = document.querySelector('#sortCheckbox');

            const rows = Array.from(table.querySelectorAll('tr')).slice(1);
            let count = 0;

            // Save filter state to localStorage
            const filterState = {
                textFilters: Array.from(textFilters).map(input => input.value),
                engCheckboxes: Array.from(engCheckboxes).map(chk => chk.value),
                langCheckboxes: Array.from(langCheckboxes).map(chk => chk.value),
                replyCheckboxes: Array.from(replyCheckboxes).map(chk => chk.value),
                approveCheckbox: approveCheckbox ? approveCheckbox.value : null,
                notApproveCheckbox: notApproveCheckbox ? notApproveCheckbox.value : null,
                sortCheckbox: sortCheckbox.checked
            };
            localStorage.setItem('filterState', JSON.stringify(filterState));

            rows.forEach(row => {
                // Check if the row contains input or select elements (edit mode)
                const hasInputs = row.querySelector('select');
                if (hasInputs) {
                    row.style.display = ''; // Ensure the row is always visible
                    return; // Skip filtering for this row
                }

                let isMatch = true;

                textFilters.forEach((filter, index) => {
                    const cell = row.getElementsByTagName('td')[index + 2];
                    if (cell && filter.value && !cell.textContent.toLowerCase().includes(filter.value.toLowerCase())) {
                        isMatch = false;
                    }
                });

                const eng = row.getElementsByTagName('td')[14]?.textContent;
                if (engCheckboxes.length > 0 && !Array.from(engCheckboxes).some(chk => eng.includes(chk.value))) {
                    isMatch = false;
                }

                const lang = row.getElementsByTagName('td')[15]?.textContent;
                if (langCheckboxes.length > 0 && !Array.from(langCheckboxes).some(chk => lang.includes(chk.value))) {
                    isMatch = false;
                }

                const replied = row.getElementsByTagName('td')[17]?.textContent;
                if (replyCheckboxes.length > 0 && !Array.from(replyCheckboxes).some(chk => replied.includes(chk.value))) {
                    isMatch = false;
                }

                const approved = row.getElementsByTagName('td')[18]?.textContent;
                if (approveCheckbox && approved !== '1') {
                    isMatch = false;
                }
                if (notApproveCheckbox && approved === '1') {
                    isMatch = false;
                }

                if (sortCheckbox.checked) {
                    const columnIndex = 7;
                    const sortedRows = Array.from(rows).filter(row => row.style.display !== 'none').sort((a, b) => {
                        const aValue = a.getElementsByTagName('td')[columnIndex]?.textContent.toLowerCase();
                        const bValue = b.getElementsByTagName('td')[columnIndex]?.textContent.toLowerCase();
                        return bValue.localeCompare(aValue);
                    });
                    sortedRows.forEach(row => table.appendChild(row));
                }

                if (isMatch) count++;
                row.style.display = isMatch ? '' : 'none';
            });
            document.getElementById('recipient-count-value').innerHTML = count;
        }
        
        function restoreFilterState() {
            const filterState = JSON.parse(localStorage.getItem('filterState'));
            if (!filterState) return;

            // Restore text filters
            const textFilters = document.querySelectorAll('.search');
            textFilters.forEach((input, index) => {
                if (filterState.textFilters[index]) {
                    input.value = filterState.textFilters[index];
                }
            });

            // Restore checkboxes
            const engCheckboxes = document.querySelectorAll('input[name="english[]"]');
            engCheckboxes.forEach(chk => {
                if (filterState.engCheckboxes.includes(chk.value)) {
                    chk.checked = true;
                }
            });

            const langCheckboxes = document.querySelectorAll('input[name="language[]"]');
            langCheckboxes.forEach(chk => {
                if (filterState.langCheckboxes.includes(chk.value)) {
                    chk.checked = true;
                }
            });

            const replyCheckboxes = document.querySelectorAll('input[name="replied[]"]');
            replyCheckboxes.forEach(chk => {
                if (filterState.replyCheckboxes.includes(chk.value)) {
                    chk.checked = true;
                }
            });

            const approveCheckbox = document.querySelector('input[name="approve"][value="1"]');
            const notApproveCheckbox = document.querySelector('input[name="approve"][value="0"]');
            if (filterState.approveCheckbox) {
                approveCheckbox.checked = true;
            }
            if (filterState.notApproveCheckbox) {
                notApproveCheckbox.checked = true;
            }

            const sortCheckbox = document.querySelector('#sortCheckbox');
            sortCheckbox.checked = filterState.sortCheckbox;

            // Reapply the filter
            filterTable();
        }

        // Call restoreFilterState when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            restoreFilterState();
        });

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
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
            localStorage.removeItem('filterState'); // Clear stored filter state
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
            const selectedRecipients = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
                .map(checkbox => checkbox.value);
            const saveTemplate = document.getElementById('save-template').checked;

            if (!message) {
                alert("Please enter a message.");
                return;
            }

            if (selectedRecipients.length === 0) {
                alert("Please select at least one recipient.");
                return;
            }

            if (confirm(`Are you sure you want to send the following message to ${selectedRecipients.length} recipients?\n\n${message}`)) {
                sendSMS(selectedRecipients, message, saveTemplate);
            }
        }

        function sendSMS(recipients, message, saveTemplate) {
            fetch('php/send_sms.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({recipients, message, saveTemplate})
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

