<?php
    session_start();
    include_once 'header.php';

?>  
    <title>Volunteer Delivery</title>
    <link rel="stylesheet" href="deliver.css">
    <script src="deliver.js" defer></script>
    <input type="hidden" name="" id="volunteerID" value="<?php echo $_SESSION['volunteerID']; ?>">
    <input type="hidden" name="" id="collected" value="<?php echo $_SESSION['collected']; ?>">


    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/supercluster@7.1.0/dist/supercluster.min.js"></script>

   
    <!-- Step 1: Phone Number Input -->
    <?php if (!isset($_SESSION['volunteerID'])): ?>
        <div class="login-div">
            <form class="login-popup" id="login-popup" action="php/volunteer_login.php" method="post">
                <label for="phone-number" English="Enter Your Volunteer's Phone Number:" Arabic="أدخل رقم هاتف المتطوع الخاص بك:" Farsi="شماره تلفن داوطلب خود را وارد کنید:" Spanish="Ingrese el número de teléfono de su voluntario:" Urdu="اپنے رضاکار کا فون نمبر درج کریں:" Myanmar="သင့်စေတနာ့ဝန်ထမ်း၏ဖုန်းနံပါတ်ကိုထည့်ပါ:" Pashto="د خپل رضاکار د تلیفون شمیره دننه کړئ:">Enter Your Volunteer's Phone Number:</label>                <input autofocus type="tel" name="phone" id="phone-number" placeholder="Phone Number">
                <button id="fetch-volunteer-btn" class="general-button">Login</button>
                <?php if (isset($_GET['error'])): ?>
                    <p class="error" English="Volunteer not found" Arabic="المتطوع غير موجود" Farsi="داوطلب یافت نشد" Spanish="Voluntario no encontrado" Urdu="رضاکار نہیں ملا" Myanmar="စေတနာ့ဝန်ထမ်းကိုမတွေ့ပါ" Pashto="رضاکار ونه موندل شو">Volunteer not found</p>                <?php endif; ?>
            </form>
        </div>
        
        <?php endif; ?>




    <div class="delivery-header-div">

        <?php if (isset($_SESSION['volunteerID'])): ?>
            <h2 id="delivery-title" style="margin-top: 0;">
            <span Arabic="السلام عليكم، " Persian="سلام علیکم، " Spanish="Hola, " Urdu="ہلا، " Myanmar="ဟာယီသို့, " Pashto="سلام علیکم، ">Hello,</span>
                <?php echo $_SESSION['volunteerName']; ?>
            </h2>
        <?php endif; ?>



        <!-- Step 2: Address Input or Locate Me Button -->

        <div class="search-bar-and-locate-div">
            <input type="text" class="search-bar" placeholder="Your Location" tabindex="0" arabic="موقعك">
            <button type="button" id="locate-button-div" id="locate-me-btn">
                <i class="fa fa-map-marker" style="font-size: var(--font-larger);"></i>
                <h5 class="search-text-locate" arabic="حدد موقعي">Locate Me</h5>
            </button>
            <button type="button" class="find-button" arabic="ابحث">Submit</button>
        </div>
        <h2 class="hidden" id="locate-me-error"></h2>

        <h3 id="recipient-header" class="hidden"></h3>
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
        <h1 style="margin-bottom: 0;" English="Summary" Arabic="ملخص" Farsi="خلاصه" Spanish="Resumen" Urdu="خلاصہ" Myanmar="အကျဉ်းချုပ်" Pashto="لنډیز">Summary</h1>        <div id="selected-recipients-header">

        </div>
        <div style="display: flex; flex-direction: row; text-align: center; justify-content: center;">
            <h2 class="center-text" style="margin: 0 !important;" English="Selected Recipients:" Arabic="المستلمون المحددون:" Farsi="دریافت کنندگان انتخاب شده:" Spanish="Destinatarios seleccionados:" Urdu="منتخب کردہ وصول کنندگان:" Myanmar="ရွေးချယ်ထားသော လက်ခံသူများ:" Pashto="ټاکل شوي ترلاسه کونکي:">Selected Recipients:</h2>  
            <h2 id="selected-recipients-count" style="color:red; margin: 0 0 0 5px !important;"></h2>

        </div>
        <div id="recipients-table">
            
        </div>
        <br>
        <br>
        <br>
    </div>

</body>
</html>
