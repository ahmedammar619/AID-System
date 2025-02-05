<?php
    include_once 'header.php';
?>
    
    <!-- Page content with translations -->
    <h1>Register Volunteer Form</h1>
    <title>Register Volunteer</title>

    <!-- Handling success and error messages -->
    <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'true') {
                echo '<p class="error">Error: Could not process the registartion</p>';
            } else {
                echo '<p class="success" Arabic="تمت معالجة المتطوع بنجاح، شكرًا لك!" Farsi="داوطلب با موفقیت پردازش شد، متشکرم!" Spanish="Voluntario procesado con éxito, ¡Gracias!" Urdu="رضاکار کامیابی سے پروسیس ہو گیا، شکریہ!" Myanmar="ကျေးဇူးတင်ပါသည်၊ လုပ်အားပေးသူကိုအောင်မြင်စွာလုပ်ဆောင်ပြီးပါပြီ!" Pashto="د رضاکار په بریالیتوب سره پروسس شو، مننه!">Volunteer registered successfully, Thank you!</p>';
            }
        }
    ?>

<?php if (isset($_GET['error']) && $_GET['error'] != 'true'): ?>
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                <h1 class="mb-5">Financial Support</h1>
                <h6 class="mb-4 text-secondary text-uppercase">Make Dua'a (Pray) for us to keep this work going and help as many people we can in the community</h6>
               
                <h6 class="center-text">American Islamic Diversity (AID) Inc. <br>is a 501(c)3, tax exempt, not for profit organization.</h6>
                <div id="paypal-button-container-P-2WE919683S386261WMXF55LI"></div>
                <script src="https://www.paypal.com/sdk/js?client-id=Add2t1sbp64e6WA6DJA4bAmKMbSDXcByjabRYTKectqS1mEentkn8kAJZehRyPC5ki4qh-Y5MIbYSN0O&amp;vault=true&amp;intent=subscription" data-sdk-integration-source="button-factory" data-uid-auto="uid_mjhnbdvtjqseghzieuoeabthzjrlbg"></script>
                <script>
                paypal.Buttons({
                    style: {
                        shape: 'rect',
                        color: 'gold',
                        layout: 'vertical',
                        label: 'subscribe'
                    },
                    createSubscription: function(data, actions) {
                        return actions.subscription.create({
                        /* Creates the subscription */
                        plan_id: 'P-2WE919683S386261WMXF55LI',
                        quantity: 1 // The quantity of the product for a subscription
                        });
                    },
                    onApprove: function(data, actions) {
                        alert(data.subscriptionID); // You can add optional success message for the subscriber here
                    }
                }).render('#paypal-button-container-P-2WE919683S386261WMXF55LI'); // Renders the PayPal button
                </script>
            </div>            
        </div>
    </div>
<?php else: ?>

    <p arabic="يرجى ملء الإستمارة للتطوع في بنك الطعام" persian="لطفا برای داوطلب شدن در بانک مواد غذایی فرم را پر کنید">Please fill out the form to volunteer at our food bank</p>
    <hr>
    
    <style>
        p{
            margin: 5px;
        }
        /* Custom Select Container */
        .custom-select {
            position: relative;
        }

        /* Style the select element */
        .custom-select select {
            width: 45%;
            font-size: 16px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .custom-select {
                max-width: 100%;
            }
        }
    </style>

    <!-- Form for the new page, e.g., Donation Form -->
    <form action="php/add_volunteer.php" method="post" id="volunteerForm" class="custom-select">
        <!-- Select Language -->
        <label for="language" Arabic="اللغة المفضلة" Persian="زبان مورد انتخاب" Spanish="Idioma preferido" Urdu="زبان متاسفانه" Myanmar="ဘာသာစကား" Pashto="زبان مورد انتخاب">Preferred Language:</label>
        <select id="language" name="language" onchange="changeLanguage()" required>
            <option value="English">English</option>
            <option value="Arabic">العربية</option>
            <option value="Farsi">فارسی</option>
            <option value="Spanish">Spanish</option>
            <option value="Urdu">Urdu</option>
            <option value="Myanmar">Burmese</option>
            <option value="Pashto">Pashto</option>
            <option value="Other">Other</option>
        </select>
        <hr>

        <label for="full_name" English="Full Name" Arabic="الإسم الكامل" Farsi="نام کامل" Spanish="Nombre completo" Urdu="پورا نام" Myanmar="အမည်" Pashto="بشپړ نوم">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>
        <hr>
        <label for="phone" English="Phone Number" Arabic="رقم الهاتف" Farsi="شماره تلفن" Spanish="Número de teléfono" Urdu="فون نمبر" Myanmar="ဖုန်းနံပါတ်" Pashto="د تلیفون شمیره">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>
        <h4 id="result" style="display: none; color:red; text-align: center; margin:5px" Arabic:"رقم الهاتف مسجل بالفعل." Farsi: "شماره تلفن قبلاً ثبت شده است." Spanish:"El número de teléfono ya está registrado." Urdu:"فون نمبر پہلے سے رجسٹرڈ ہے۔" Myanmar:"ဖုန်းနံပါတ်သည် ယခင်ကတည်းစာရင်းသွင်းပြီးဖြစ်သည်။" Pashto:"د تلیفون شمیره دمخه ثبت شوې ده.">Phone number is already registered.</h4>
        <script>
            document.getElementById('phone').addEventListener('focusout', function() {
                const phone = this.value; // Get the phone number
                const type = 'volunteer'; // or 'volunteer', depending on your logic

                if (!phone) return; // Exit if the phone number is empty

                // Send a GET request to the PHP script
                fetch(`php/check_phone_validity.php?phone=${encodeURIComponent(phone)}&type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.registered) {
                            document.getElementById('result').style.display = 'block';

                        } else {
                            // Clear the warning message if the phone number is unique
                            document.getElementById('result').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        </script>
        <hr>
        <label for="email" English="Email" Arabic="البريد الإلكتروني" Farsi="پست الکترونیک" Spanish="Correo electrónico" Urdu="ای میل" Myanmar="အီးမေးလ်" Pashto="بریښنالیک">Email:</label>
        <input type="email" id="email" name="email" required>
        <hr>
        <label for="zip_code" English="Zip Code" Arabic="الرمز البريدي" Farsi="کد پستی" Spanish="Código postal" Urdu="زپ کوڈ" Myanmar="စာတိုက်သင်္ကေတ" Pashto="د زپ کوډ">Zip Code:</label>
        <input type="number" id="zip_code" name="zip_code" required>
        <hr>

        <label for="preference" Arabic="كيف ترغب في التطوع؟" Farsi="چگونه می‌خواهید داوطلب شوید؟" Spanish="¿Cómo te gustaría ser voluntario?" Urdu="آپ کس طرح رضاکار بننا چاہیں گے؟" Myanmar="သင်ဘယ်လိုလုပ်အားပေးချင်ပါသလဲ။" Pashto="تاسو څنګه د رضاکارۍ غواړئ؟">How would you like to volunteer?</label>
        <select id="preference" name="preference" required>
            <option value="" disabled selected>Select an option</option>
            <option value="Reminder every month" Arabic="تذكير كل شهر" Farsi="یادآوری هر ماه" Spanish="Recordatorio cada mes" Urdu="ہر مہینے یاد دہانی" Myanmar="လတိုင်းသတိပေးချက်" Pashto="هر میاشت یادونه">Reminder every month</option>
            <option value="Committed every month" Arabic="ملتزم كل شهر" Farsi="متعهد هر ماه" Spanish="Comprometido cada mes" Urdu="ہر مہینے پابند" Myanmar="လတိုင်းကတိပြုသည်" Pashto="هر میاشت ژمنه">Committed every month</option>
            <option value="Committed one time" Arabic="ملتزم لمرة واحدة" Farsi="متعهد یک بار" Spanish="Comprometido una vez" Urdu="ایک بار پابند" Myanmar="တစ်ကြိမ်တာကတိပြုသည်" Pashto="یو ځل ژمنه">Committed one time</option>
        </select>
        <hr>

        <label for="car_size" Arabic="عدد الصناديق لحجم سيارتك (إن وجد):" Farsi="تعداد جعبه‌ها برای اندازه ماشین شما (در صورت وجود):" Spanish="Número de cajas para el tamaño de tu coche (si corresponde):" Urdu="آپ کی گاڑی کے سائز کے لیے ڈبوں کی تعداد (اگر لاگو ہو):" Myanmar="သင့်ကားအရွယ်အစားအတွက် သေတ္တာများ (လိုအပ်ပါက):" Pashto="د موټر د اندازې لپاره د بکسونو شمیر (که اړین وي):">Number of Boxes for your Car Size (if applicable):</label>
        <select id="car_size" name="car_size" value="none">
            <option disabled selected>Select an option</option>
            <option value="none" Arabic="لا يوجد سيارة" Farsi="بدون ماشین" Spanish="Sin coche" Urdu="گاڑی نہیں" Myanmar="ကားမရှိ" Pashto="موټر نشته">No Car</option>
            <option value="6" Arabic="صغيرة ذات بابين (6)" Farsi="کوچک دو در (6)" Spanish="Pequeño 2 puertas (6)" Urdu="چھوٹی 2-دروازہ (6)" Myanmar="သေးငယ်သော 2-တံခါး (6)" Pashto="وړوکی 2-ور (6)">Small 2-Door (6)</option>
            <option value="8" Arabic="متوسطة ذات 4 أبواب (8)" Farsi="متوسط 4 در (8)" Spanish="Mediano 4 puertas (8)" Urdu="درمیانہ 4-دروازہ (8)" Myanmar="အလတ်စား 4-တံခါး (8)" Pashto="منځنۍ 4-ور (8)">Medium 4-Door (8)</option>
            <option value="12" Arabic="SUV (12)" Farsi="SUV (12)" Spanish="SUV (12)" Urdu="SUV (12)" Myanmar="SUV (12)" Pashto="SUV (12)">SUV (12)</option>
            <option value="18" Arabic="فان (18)" Farsi="ون (18)" Spanish="Furgoneta (18)" Urdu="ویں (18)" Myanmar="ဗန်း (18)" Pashto="وان (18)">Van (18)</option>
            <option value="48" Arabic="شاحنة صغيرة (48)" Farsi="پیکاپ (48)" Spanish="Camioneta (48)" Urdu="پک اپ ٹرک (48)" Myanmar="ပစ်ကား (48)" Pashto="پیک اپ (48)">Pickup Truck (48)</option>
        </select>
        <label for="car_size" Arabic="صناديق" Farsi="جعبه‌ها" Spanish="Cajas" Urdu="ڈبے" Myanmar="သေတ္တာများ" Pashto="بکسونه">Boxes</label>
        <hr>
        <style>
            input[type="checkbox"]{
                height: 20px;
                margin-top: 15px;
                margin-right: 10px;
            }
        </style>
        <label for="event_names" Arabic="أود التطوع والحصول على تذكيرات للأحداث التالية (اختر جميع ما ينطبق):" Persian="می‌خواهم داوطلب شوم و برای رویدادهای زیر یادآوری دریافت کنم (همه موارد مربوطه را انتخاب کنید):" Spanish="Me gustaría ser voluntario y recibir recordatorios para los siguientes eventos (seleccione todos los que correspondan):" Urdu="میں رضاکار بننا چاہتا ہوں اور درج ذیل تقریبات کے لیے یاددہانیاں حاصل کرنا چاہتا ہوں (تمام لاگو ہونے والے کو منتخب کریں):" Myanmar="အောက်ပါပွဲများအတွက် စေတနာ့ဝန်ထမ်းလုပ်ပြီး အသိပေးချက်များရယူလိုပါသည် (သက်ဆိုင်ရာများကို အားလုံးရွေးပါ):" Pashto="زه غواړم د رضاکارۍ لپاره شامل شم او د لاندې پیښو لپاره یادښتونه ترلاسه کړم (ټول اړونده انتخاب کړئ):">I would like to volunteer and get reminders for the following events (select all that apply):</label><br>
        <!-- Food Pantry Packing -->
        <input type="checkbox" id="Food_Pantry_Packing" name="event_names[]" value="Food Pantry Packing"><label for="Food_Pantry_Packing" Arabic="تعبئة مخزن الطعام" Persian="بسته‌بندی مواد غذایی" Spanish="Empaquetado de despensa de alimentos" Urdu="فوڈ پینٹری پیکنگ" Myanmar="အစားအစာသိုလှောင်ရုံထုပ်ပိုးခြင်း" Pashto="د خوراکي ذخیره بسته‌بندي">Food Pantry Packing</label><br>
        <!-- Food Pantry Delivery -->
        <input type="checkbox" id="Food_Pantry_Delivery" name="event_names[]" value="Food Pantry Delivery"><label for="Food_Pantry_Delivery" Arabic="توصيل مخزن الطعام" Persian="تحویل مواد غذایی" Spanish="Entrega de despensa de alimentos" Urdu="فوڈ پینٹری ڈیلیوری" Myanmar="အစားအစာသိုလှောင်ရုံပို့ဆောင်ခြင်း" Pashto="د خوراکي ذخیره تحویلي">Food Pantry Delivery</label><br>
        <!-- Iftar Preparation -->
        <input type="checkbox" id="Iftar_Preparation" name="event_names[]" value="Iftar Preparation"><label for="Iftar_Preparation" Arabic="تحضير الإفطار" Persian="آماده‌سازی افطار" Spanish="Preparación del Iftar" Urdu="افطار کی تیاری" Myanmar="အစ္စလာမ်အစားအစာပြင်ဆင်ခြင်း" Pashto="د افطار چمتووالی">Iftar Preparation</label><br>
        <!-- Iftar Delivery -->
        <input type="checkbox" id="Iftar_Delivery" name="event_names[]" value="Iftar Delivery"><label for="Iftar_Delivery" Arabic="توصيل الإفطار" Persian="تحویل افطار" Spanish="Entrega del Iftar" Urdu="افطار کی ڈیلیوری" Myanmar="အစ္စလာမ်အစားအစာပို့ဆောင်ခြင်း" Pashto="د افطار تحویلي">Iftar Delivery</label><br>
        <!-- Ramadan Basket Delivery -->
        <input type="checkbox" id="Ramadan_Basket_Delivery" name="event_names[]" value="Ramadan Basket Delivery"><label for="Ramadan_Basket_Delivery" Arabic="توصيل سلة رمضان" Persian="تحویل سبد رمضان" Spanish="Entrega de canasta de Ramadán" Urdu="رمضان باسکٹ ڈیلیوری" Myanmar="ရမဒန်ကောင်တာပို့ဆောင်ခြင်း" Pashto="د رمضان سبد تحویلي">Ramadan Basket Delivery</label><br>
        <!-- Gift Wrapping -->
        <input type="checkbox" id="Gift_Wrapping" name="event_names[]" value="Gift Wrapping"><label for="Gift_Wrapping" Arabic="تغليف الهدايا" Persian="بسته‌بندی هدیه" Spanish="Envoltura de regalos" Urdu="گفٹ ریپنگ" Myanmar="လက်ဆောင်ထုပ်ပိုးခြင်း" Pashto="د ډالۍ بسته‌بندي">Gift Wrapping</label><br>
        <!-- Gift Delivery -->
        <input type="checkbox" id="Gift_Delivery" name="event_names[]" value="Gift Delivery"><label for="Gift_Delivery" Arabic="توصيل الهدايا" Persian="تحویل هدیه" Spanish="Entrega de regalos" Urdu="گفٹ ڈیلیوری" Myanmar="လက်ဆောင်ပို့ဆောင်ခြင်း" Pashto="د ډالۍ تحویلي">Gift Delivery</label><br>
        <!-- Bazaar Preparation -->
        <input type="checkbox" id="Bazaar_Preparation" name="event_names[]" value="Bazaar Preparation"><label for="Bazaar_Preparation" Arabic="تحضير البازار" Persian="آماده‌سازی بازار" Spanish="Preparación del bazar" Urdu="بازار کی تیاری" Myanmar="ဈေးတန်းပြင်ဆင်ခြင်း" Pashto="د بازار چمتووالی">Bazaar Preparation</label><br>
        <!-- Bazaar Volunteering -->
        <input type="checkbox" id="Bazaar_Volunteering" name="event_names[]" value="Bazaar Volunteering"><label for="Bazaar_Volunteering" Arabic="التطوع في البازار" Persian="داوطلب شدن در بازار" Spanish="Voluntariado en el bazar" Urdu="بازار میں رضاکارانہ خدمات" Myanmar="ဈေးတန်းစေတနာ့ဝန်ထမ်းလုပ်ခြင်း" Pashto="د بازار رضاکاري">Bazaar Volunteering</label><br>
        <!-- Adha Meat Delivery -->
        <input type="checkbox" id="Adha_Meat_Delivery" name="event_names[]" value="Adha Meat Delivery"><label for="Adha_Meat_Delivery" Arabic="توصيل لحوم الأضاحي" Persian="تحویل گوشت قربانی" Spanish="Entrega de carne de Adha" Urdu="قربانی کا گوشت ڈیلیوری" Myanmar="အဒ္ဓါအသားပို့ဆောင်ခြင်း" Pashto="د قرباني غوښه تحویلي">Adha Meat Delivery</label><br>
        <!-- Furniture Pickup & Delivery -->
        <input type="checkbox" id="Furniture_Pickup_Delivery" name="event_names[]" value="Furniture Pickup & Delivery"><label for="Furniture_Pickup_Delivery" Arabic="استلام وتوصيل الأثاث" Persian="تحویل و تحویل مبلمان" Spanish="Recogida y entrega de muebles" Urdu="فرنیچر اٹھانا اور ڈیلیوری" Myanmar="ပရိဘောဂပစ္စည်းများကောက်ယူခြင်းနှင့်ပို့ဆောင်ခြင်း" Pashto="د فرنیچر راټولول او تحویلي">Furniture Pickup & Delivery</label><br>
        <!-- Any Other Events -->
        <input type="checkbox" id="Any_Other_Events" name="event_names[]" value="Any Other Events"><label for="Any_Other_Events" Arabic="أي أحداث أخرى" Persian="هر رویداد دیگر" Spanish="Cualquier otro evento" Urdu="کوئی دوسری تقریبات" Myanmar="အခြားမည်သည့်ပွဲများ" Pashto="هر بل پیښه">Any Other Events</label><br>
        <hr>

        <a id="terms" style="text-align: center; display: block; margin: 0 0 0 0;" href="https://www.fns.usda.gov/civil-rights/usda-nondiscrimination-statement" target="_blank">Link</a>
        <div class="label-input">
            <input type="radio" id="policy_check" name="policy_check" required>
            <label for="policy_check" English="I agree to the USDA Non-Discrimination Policy and confirm that I have read and understood the terms." 
                Arabic="أوافق على سياسة عدم التمييز الخاصة بوزارة الزراعة الأمريكية وأؤكد أنني قد قرأت وفهمت الشروط." 
                Farsi="من با سیاست عدم تبعیض USDA موافقم و تأیید می‌کنم که شرایط را خوانده‌ام و درک کرده‌ام." 
                Spanish="Estoy de acuerdo con la Política de No Discriminación del USDA y confirmo que he leído y entendido los términos." 
                Urdu="میں USDA کی عدم امتیاز کی پالیسی سے متفق ہوں اور تصدیق کرتا ہوں کہ میں نے شرائط کو پڑھ اور سمجھ لیا ہے۔" 
                Myanmar="ကျွန်ုပ်သည် USDA ၏ မခွဲခြားရေးမူဝါဒကို သဘောတူပြီး စည်းကမ်းချက်များကို ဖတ်ရှုနားလည်ကြောင်း အတည်ပြုပါသည်။" 
                Pashto="زه د USDA د توپیر نه کولو پالیسي سره موافق یم او تایید کوم چې ما شرطونه لوستلي او پوه شوي یم.">
                I agree to the <a href="https://www.fns.usda.gov/civil-rights/usda-nondiscrimination-statement" target="_blank">USDA Non-Discrimination Policy</a> and confirm that I have read and understood the terms.
            </label>
        </div>
        <style>
            .label-input a , #terms{
                color: #007BFF; /* Blue color for the link */
                text-decoration: underline;
            }
            .label-input a:hover , #terms:hover{
                color: #0056b3; /* Darker blue on hover */
            }
            .label-input input[type="radio"] {
                margin-right: 10px;
            }
        </style>
        <hr>

        <label for="comment">Additional Comments:</label><br>
        <textarea id="comment" name="comment" maxlength="255" rows="3" cols="50"></textarea>
        <hr>

        <input class="general-button" type="submit" value="Submit">
    </form>
    <script>
            translations = {
        "Please enter text in English only.": {
            English: "Please enter text in English only.",
            Arabic: "يرجى إدخال النص باللغة الإنجليزية فقط.",
            Farsi: "لطفاً نوشته به متن فارسی فقط.",
            Spanish: "Por favor, ingrese el texto en inglés solo.",
            Urdu: "براہ کرم ترجمہ انگریزی میں لکھیں.",
            Myanmar: "ကျေးဇူးပြု၍ အကြောင်းပြန်ချက်များကို ဖြည့်ပါ။",
            Pashto: "مهرباني وکړئ د ترجمہ انگریزی میں لکړئ تاسو ډاډه یاست چې غواړئ."
        }
    }

    const language = localStorage.getItem('preferredLanguage');
    const inputFields = document.querySelectorAll("input");
    console.log(language);
    for (const inputField of inputFields) {
        if (inputField.type === "text") {
            inputField.addEventListener("keyup", function(event) {
                if (!validateEnglish(event.target.value)) {
                    alert(translations['Please enter text in English only.'][language]);
                    inputField.value = "";
                    inputField.focus();
                }
            });
        }
    }

    </script>

<?php endif; ?>

<?php
    include_once 'footer.php';
?>
