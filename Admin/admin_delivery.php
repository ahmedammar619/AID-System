<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require('../php/db.php');

if(!isset($_SESSION['admin_id'])){
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        header{
            min-height: 110px !important;
            z-index: 1;
        }
        #map{
            height: 1000px !important;
        }
        @media (max-width: 600px) {
            header h1{
                font-size: large;
            }
            header nav{
                font-size: medium;
            }
        }
    </style>
    <title>Admin Delivery</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../deliver.css">
    <link rel="stylesheet" href="../general.css"/>
    <link rel="stylesheet" href="styles.css">
    <script src="admin_delivery.js" defer></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="FONTAWESOME_API_KEY" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/supercluster@7.1.0/dist/supercluster.min.js"></script>
    
</head>
<body>
    <header>
        <h1>Manage Data</h1>
        <nav>
            <ul>
                <li><a href="admin_logs.php">Admin</a></li>
                <li><a href="admin_volunteers.php">Volunteers</a></li>
                <li><a href="admin_recipients.php">Recipients</a></li>
                <li><a href="admin_volunteers_excel.php">Vol.xlsx</a></li>
                <li><a href="admin_recipients_excel.php">Rec.xlsx</a></li>
                <li><a href="admin_delivery.php">Delivery</a></li>
                <li><a href="php/logout.php"><i class="fa fa-sign-out"></i></a></li>
            </ul>
        </nav>
    </header>
    
    
    <input type="hidden" name="" id="volunteerID" value="<?php echo $_SESSION['volunteerID']; ?>">
    <input type="hidden" name="" id="collected" value="<?php echo $_SESSION['collected']; ?>">
    <input type="hidden" name="" id="volunteerName" value="<?php echo $_SESSION['volunteerName']; ?>">

    <div class="delivery-header-div">
                <!-- Step 2: Address Input or Locate Me Button -->

            <!-- <div class="search-area"> -->
                <div class="search-bar-and-locate-div">
                    <input type="text" class="search-bar" placeholder="Your Location" tabindex="0" arabic="موقعك">
                    <button type="button" id="locate-button-div" id="locate-me-btn">
                        <i class="fa fa-map-marker" style="font-size: var(--font-larger);"></i>
                        <h5 class="search-text-locate" arabic="حدد موقعي">Locate Me</h5>
                    </button>
                    <button type="button" class="find-button" arabic="ابحث">Submit</button>
                </div>
                <h2 class="hidden" id="locate-me-error"></h2>
            <!-- </div> -->
            <style>
                .delivery-header-div{
                    background-color: white;
                    border: 2px solid black;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    margin: 0;
                    border-radius: 0px 0 20px 20px;
                    padding: 10px;
                }
                .select-volunteer-div{
                    box-shadow: 0 0 0 0;
                    border: 0;
                    background-color: white;
                    margin: 0;
                    padding-top: 0;
                    padding-bottom: 0;
                    min-width: 380px !important;
                }


                .volunteer-input-div{
                    display: grid;
                    gap: 10px;
                    justify-content: center;
                    /* make the split of 3 items as 3 3 1 */
                    grid-template-columns: 3fr 1fr .5fr;
                    text-align: center;
                }
            </style>
            <hr>
            <form class="select-volunteer-div" id="" action="php/admin_volunteer_login.php" method="post">
                <h3 style="margin:5px">Select Recipients For Each Volunteer</h3>
                <div class="volunteer-input-div">
                    <input type="tel" name="phone" id="phone-number" placeholder="Volunteer Phone Number">
                    <button id="fetch-volunteer-btn" class="general-button">Select</button>
                    <button id="reset" class="general-button" style="background-color: red;"><i class="fa-solid fa-trash-can"></i></button>
                    <script>
                        document.getElementById('reset').addEventListener('click', function() {
                            document.getElementById('phone-number').value = 0;
                        });
                    </script>
                </div>
                <?php if (isset($_GET['error']) && $_GET['error'] == 'true'): ?>
                    <p class="error">Volunteer not found</p>
                <?php endif; ?>
                <h3 id="delivery-title" style="margin: 5px 0 0 0 !important;">Volunteer: <span style="color:green"><?php echo $_SESSION['volunteerName']??'Not Selected',' | '. $_SESSION['volunteerPhone']??''; ?></span></h3>
                <h3 id="recipient-header" class="hidden" style="margin:5px 0px 0px 0"></h3>
            </form>
            <button class="general-button" id="general-confirm-button" type="button" style="background-color: green; margin-top: 10px; display:none;"></button>
        </div>


        <!-- ask for address when user is logged -->



    <!-- Step 3: Map Section -->
    <div id="map"></div>
    <div class="show-recipient-marker-div"></div>

    <!-- Step 4: Recipients List -->
    <div id="recipient-header">
        <!-- Recipients will be dynamically added here -->
    </div>

    <!-- Step 5: Summary of Selected Recipients -->
    <div id="summary">
        <div class="summary-button-div">
            <button class="fa fa-arrow-up bottom-btn" id="" aria-hidden="true"></button>
            <button style="display: none;" class="fa fa-arrow-down bottom-btn" aria-hidden="true"></button>
        </div>
        <h1 style="margin-bottom: 0;">Summary</h1>
        <div id="selected-recipients-header">

        </div>
        <h2 class="center-text" style="margin: 0 !important;">Selected Recipients:</h2>
        <div id="recipients-table">
            
        </div>
        <br>
        <br>
        <br>
    </div>

    </main>

</body>
</html>