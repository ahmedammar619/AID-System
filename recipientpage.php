<?php
session_start();
include_once 'header.php';
?>
    <title>Edit Recipient</title>
    <link rel="stylesheet" href="recipientpage.css">
    <script src="recipientpage.js" defer></script>
    <?php
        if (isset($_GET['recierror'])) {
            if ($_GET['error'] == 'true') {
                echo '<p class="error">Error: Could not update recipient</p>';
            } 
            // else {
            //     echo '<p class="success">Recipient registered successfully</p>';
            // }
        }
    ?>
    <?php if (!isset($_SESSION['recipientID'])): ?>
    <div class="login-div">
        <form class="login-popup" id="login-popup" action="php/recipient_login.php" method="post">
                <label for="phone-number" Arabic="رقم هاتف المستلم" Farsi="شماره تلفن مستلم" Persian="شماره تلفن مستلم" Spanish="Número de teléfono del destinatario" Urdu="فون نمبر" Myanmar="ဖုန်းနံပါတ်" Pashto="د تلیفون شمیره">Recipient Phone Number:</label>
            
                <input autofocus type="tel" name="phone" id="phone-number" placeholder="Phone Number">
                <button id="fetch-volunteer-btn" class="general-button">Login</button>
                <?php if (isset($_GET['error'])): ?>
                    <p class="error">Recipient not found</p>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['recipientID'])): ?>
    <?php endif; ?>


    


    <div class="profile-container" method="">
        <h1 >Recipient Profile</h1>
        <h2 style="margin-bottom: 10px;" class="center-text">
            <span Arabic="السلام عليكم، " Persian="سلام علیکم، " Spanish="Hola, " Urdu="ہلا، " Myanmar="ဟာယီသို့, " Pashto="سلام علیکم، ">Hello,</span>
            <span><?php echo $_SESSION['recipientName']; ?></span>
        </h2>

        <!-- <div id="edit-first_name">
            <h3 Arabic="الاسم الكامل" Farsi="نام کامل" Spanish="Nombre completo" Urdu="پورا نام" Myanmar="အမည်" Pashto="بشپړ نوم">Full Name:</h3>
            <p id="edit-first_name"><?php echo $_SESSION['recipientName']?></p>
            <input class="hidden general-input" name="full_name" type="text" value="<?php echo $_SESSION['recipientName']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-first_name">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr> -->
        
        <div id="edit-email">
            <h3 arabic="البريد الإلكتروني" Farsi="ایمیل" Spanish="Correo electrónico" Urdu="ای میل" Myanmar="အီးမေး" Pashto="برېړنی بریښنا">Email:</h3>
            <p id="edit-email"><?php echo $_SESSION['recipientEmail']?></p>
            <input class="hidden general-input" name="email" type="text" value="<?php echo $_SESSION['recipientEmail']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-email">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>

        <div id="edit-phone">
            <h3 arabic="رقم الهاتف" Farsi="تلفن" Spanish="Teléfono" Urdu="فون" Myanmar="ဖုန်း" Pashto="د تلیفون شمیره">Phone:</h3>
            <p id="edit-phone"><?php echo $_SESSION['recipientPhone']?></p>            
            <input class="hidden general-input" name="phone" type="text" value="<?php echo $_SESSION['recipientPhone']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-phone">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-apt_num">
            <h3 arabic="رقم الشقة" Farsi="آپارٹمنٹ نمبر" Spanish="Número de apartamento" Urdu="فلیٹ نمبر" Myanmar="အပတ်နံပါတ်" Pashto="د آپارټمنټ شمیره">Apartment Number:</h3>
            <p id="edit-apt_num"><?php echo $_SESSION['recipientAptNum']?></p>
            <input class="hidden general-input" name="apt_num" type="text" value="<?php echo $_SESSION['recipientAptNum']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-apt_num">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-address">
            <h3 arabic="العنوان" Farsi="آدرس" Spanish="Dirección" Urdu="پتہ" Myanmar="လိပ်စာ" Pashto="پته">Address:</h3>
            <p id="edit-address"><?php echo $_SESSION['recipientAddress']?></p>
            <input class="hidden general-input" name="address" type="text" value="<?php echo $_SESSION['recipientAddress']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-address">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-city">
            <h3 arabic="المدينة" Farsi="شهر" Spanish="Ciudad" Urdu="شہر" Myanmar="မြို့" Pashto="ښار">City:</h3>
            <p id="edit-city"><?php echo $_SESSION['recipientCity']?></p>
            <input class="hidden general-input" name="city" type="text" value="<?php echo $_SESSION['recipientCity']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-city">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-zip_code">
            <h3 arabic="الرمز البريدي" Farsi="کد پستی" Spanish="Código postal" Urdu="پسٹی کوڈ" Myanmar="စာတိုက်သင်္ကေတ" Pashto="د پسټی کوډ">Zip Code:</h3>
            <p id="edit-zip_code"><?php echo $_SESSION['recipientZipCode']?></p>
            <input class="hidden general-input" name="zip_code" type="text" value="<?php echo $_SESSION['recipientZipCode']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-zip_code">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-hotel_info">
            <h3 arabic="المعلومات الفندقية" Farsi="اطلاعات هتل" Spanish="Información del hotel" Urdu="ہوٹل کی معلومات" Myanmar="ဟိုတယ်အချက်အလက်" Pashto="هوتل معلومات">Hotel Information:</h3>
            <p id="edit-hotel_info"><?php echo $_SESSION['recipientHotelInfo']?></p>
            <input class="hidden general-input" name="hotel_info" type="text" value="<?php echo $_SESSION['recipientHotelInfo']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-hotel_info">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-gate_code">
            <h3 arabic="رمز البوابة" Farsi="کد دروازه" Spanish="Código de la puerta" Urdu="گیٹ کوڈ" Myanmar="ဂိတ်ကုဒ်" Pashto="د دروازې کوډ">Gate Code:</h3>
            <p id="edit-gate_code"><?php echo $_SESSION['recipientGateCode']?></p>
            <input class="hidden general-input" name="gate_code" type="text" value="<?php echo $_SESSION['recipientGateCode']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-gate_code">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
        <hr>
        <div id="edit-comp_name">
            <h3 arabic="اسم المجمع" Farsi="نام مجتمع" Spanish="Nombre del complejo" Urdu="کمپلیکس کا نام" Myanmar="ကွန်ပလက်အမည်" Pashto="د کمپلېکس نوم">Complex Name:</h3>
            <p id="edit-comp_name"><?php echo $_SESSION['recipientComplexName']?></p>
            <input class="hidden general-input" name="comp_name" type="text" value="<?php echo $_SESSION['recipientComplexName']?>">
            <button type="button" class="general-button edit-btn" data-edit="edit-comp_name">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>


        <?php 
            if($_GET["profile"] == "emailexist"){                            
                echo " <div class=\"red-alert\">
                <i class=\"fa fa-circle-info \">  </i>
                <h4 arabic=\"البريد الإلكتروني موجود بالفعل\">Email already exists!</h4>
            </div>";
            }
            else if($_GET["profile"] == "phoneexist"){
                echo " <div class=\"red-alert\">
                <i class=\"fa fa-circle-info \">  </i>
                <h4 arabic=\"رقم الهاتف موجود بالفعل\">Phone number already exists!</h4>
            </div>";
            }
            else if($_GET["profile"] == "invalidphone"){
                echo " <div class=\"red-alert\">
                <i class=\"fa fa-circle-info \">  </i>
                <h4 arabic=\"رقم الهاتف غير صالح\">Invalid phone number!</h4>
            </div>";
            }
            else if($_GET["profile"] == "invalidemail"){
                echo " <div class=\"red-alert\">
                <i class=\"fa fa-circle-info \">  </i>
                <h4 arabic=\"البريد الإلكتروني غير صالح\">Invalid email!</h4>
            </div>";
            }
        ?>
    </div>
<!-- 
            <form class="profile-container" action="./zPHP/refresh_user.inc.php" method="POST">
                <i class="fa fa-times profile-exit"></i>
                <i class="fa-solid fa-circle-left profile-back "></i>    
                <h2 id="editLabel" arabic="أدخل الجديد">Enter new</h2>
                <input id="editInput" placeholder="Enter Password" name="editValue" arabic="أدخل كلمة المرور">
                <input id="editInput2" placeholder="Re-enter Password" type="password" arabic="إعادة إدخال كلمة المرور">
                <select id="editSelect" name="editValueGender">
                    <option value="M" arabic="رجل">Male</option>
                    <option value="F" arabic="مرأة">Female</option>
                </select>
                <input type="hidden" id="editField" name="editField">
                <div class="red-alert" id="pass-red-alert" style="display: none;">
                    <i class="fa fa-circle-info"></i>
                    <h4 arabic="كلمات المرور غير متطابقة أو أقل من 8 أحرف">Passwords not matching or less than 8 characters!</h4>
                </div>
                <button id="saveButton" name="submit" type="submit" class="login-and-create-button confirm-button" disabled arabic="حفظ">Save</button>
            </form> -->



</body>
</html>
