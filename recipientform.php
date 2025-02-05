<?php
    include_once 'header.php';
?>
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
    <title>Register Recipient</title>
    <h1 English="Register Recipient Form" Arabic="نموذج تسجيل المستفيد" Farsi="فرم ثبت نام گیرنده" Spanish="Formulario de registro del beneficiario" Urdu="وصول کنندہ رجسٹریشن فارم" Myanmar="လက်ခံသူမှတ်ပုံတင်ပုံစံ" Pashto="د ترلاسه کوونکي ثبت نام فورم">Register Recipient Form</h1>
    <h2 English="Please fill all fields in English Only!"  Arabic="يرجى ملء جميع الحقول باللغة الإنجليزية فقط!"  Farsi="لطفاً تمام فیلدها را فقط به زبان انگلیسی پر کنید!" Spanish="¡Por favor, complete todos los campos solo en inglés!" Urdu="براہ کرم تمام فیلڈز صرف انگریزی میں بھریں!" Myanmar="ကျေးဇူးပြု၍ အကွက်အားလုံးကို အင်္ဂလိပ်ဘာသာဖြင့်သာ ဖြည့်ပါ!" Pashto="مهرباني وکړئ ټول فیلډونه یوازې په انګلیسي ډک کړئ!" style="color: red; margin: 0 0 5px 0">Please fill all fields in English Only!</h2>
    </h2> 
   <p Arabic="السلام عليكم أيها الإخوة والأخوات"Persian="درود بر شما برادران و خواهران"Farsi="سلام علیکم این اخواها و اخواتان"Pashto="درود بر شما برادران و خواهران"Spanish="La paz sea con vosotros hermanos y hermanas"Urdu="درود بر شما برادران و خواهران"Myanmar="အမေရိကန်းမား အမေရိကန်းလ်တား">Assalamu Alaykom Our Brothers & Sisters</p>
    <p Arabic="يرجى ملء الاستمارة للتسجيل كمتلقي"Persian="لطفاً فرم را برای ثبت نام به عنوان دریافت کننده پر کنید"Farsi="لطفا فرم را برای ثبت نام به عنوان دریافت کننده پر کنید"Pashto="مهرباني وکړئ د ترلاسه کونکي په توګه د ثبت لپاره فورمه ډکه کړئ"Spanish="Por favor, complete el formulario para registrarse como receptor"Urdu="براہ کرم فارم کو وصول کنندہ کے طور پر رجسٹر کرنے کے لیے پُر کریں"Myanmar="စာရင်းသွင်းသူအဖြစ် မှတ်ပုံတင်ရန် ဖောင်ကို ဖြည့်ပါ">Please fill out the form to register as a recipient</p>

    <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'true') {
                echo '<p class="error" Arabic="خطأ: لم يتمكن من تسجيل المستفيد" Farsi="خطا: گیرنده ثبت نشد" Spanish="Error: No se pudo registrar al destinatario" Urdu="خرابی: مستفید کو رجسٹر نہیں کیا جا سکا" Myanmar="Error: Recipient မှတ်ပုံတင်၍မရခဲ့ပါ" Pashto="خطا: ګټه اخیستونکی ثبت نشو">Error: Could not register recipient
                      </p>';
            } else if ($_GET['error'] == 'phone') {
                echo '<p class="error" Arabic="رقم الهاتف غير صالح" Farsi="شماره تلفن معتبر نیست" Spanish="Número de teléfono no válido" Urdu="فون نمبر غلط ہے" Myanmar="ဖုန်းနံပါတ် မရှိပါ" Pashto="تلفن نامعتبر">Phone number is not valid</p>';
            }
            else {
                echo '<p class="success" Arabic="تم تسجيل المستفيد بنجاح" Farsi="گیرنده با موفقیت ثبت شد"  Spanish="El destinatario se registró con éxito" Urdu="مستفید کامیابی کے ساتھ رجسٹر ہو گیا" Myanmar="Recipient မှာအောင်မြင်စွာ မှတ်ပုံတင်ပြီး" Pashto="ګټه اخیستونکی په بریالیتوب سره ثبت شو">Recipient registered successfully
                    </p>';
            }
        }
    ?>
    <hr>
    <form action="php/add_recipient.php" method="post" id="recipientForm" class="custom-select">
        <label for="language" Arabic="اللغة المفضلة" Persian="زبان مورد انتخاب" Spanish="Idioma preferido" Urdu="زبان متاسفانه" Myanmar="ဘာသာစကား" Pashto="زبان مورد انتخاب">Preferred Language:</label>
        <select id="language" name="language" onchange="changeLanguage()">
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
        <label for="english" English="English Proficiency" Arabic="إجادة اللغة الإنجليزية" Farsi="تسلط به زبان انگلیسی" Spanish="Dominio del inglés" Urdu="انگریزی مہارت" Myanmar="အင်္ဂလိပ်စာကျွမ်းကျင်မှု" Pashto="د انګلیسي ژبې مهارت">English Proficiency:</label>
        <select id="english" name="english" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="Fluent" English="Fluent" Arabic="طليق" Farsi="مسلط" Spanish="Fluido" Urdu="ماہر" Myanmar="ကျွမ်းကျင်" Pashto="روان">Fluent</option>
            <option value="Intermediate" English="Intermediate" Arabic="متوسط" Farsi="متوسط" Spanish="Intermedio" Urdu="درمیانہ" Myanmar="အလယ်အလတ်" Pashto="منځنی">Intermediate</option>
            <option value="Basic" English="Basic" Arabic="أساسي" Farsi="پایه" Spanish="Básico" Urdu="بنیادی" Myanmar="အခြေခံ" Pashto="اساسي">Basic</option>
            <option value="None" English="None" Arabic="لا شيء" Farsi="هیچکدام" Spanish="Ninguno" Urdu="کوئی نہیں" Myanmar="မရှိပါ" Pashto="هیڅ نه">None</option>
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
                const type = 'recipient'; // or 'volunteer', depending on your logic

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
        <label for="gender" English="Gender" Arabic="الجنس" Farsi="جنسیت" Spanish="Género" Urdu="صنف" Myanmar="လိင်" Pashto="جنس">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="Male" English="Male" Arabic="ذكر" Farsi="مرد" Spanish="Masculino" Urdu="مذكر" Myanmar="နှင့်" Pashto="ماسولو">Male</option>
            <option value="Female" English="Female" Arabic="انثي" Farsi="خانم" Spanish="Femenino" Urdu="خانم" Myanmar="နှစ်" Pashto="خانو">Female</option>
        </select>
        <hr>
        <label for="age" English="Age" Arabic="العمر" Farsi="عمر" Spanish="Edad" Urdu="عمر" Myanmar="နှစ်" Pashto="عمر">Age:</label>
        <input type="number" id="age" name="age" min="16" required>         
        <hr>
        <label for="work_status" English="Work Status" Arabic="حالة العمل" Farsi="وضعیت کار" Spanish="Estado laboral" Urdu="کام کی حالت" Myanmar="အလုပ်အခြေအနေ" Pashto="د کار حالت">Work Status:</label>
        <select id="work_status" name="work_status" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="No Permit" English="No Work Permit" Arabic="لا يوجد تصريح عمل" Farsi="بدون مجوز کار" Spanish="Sin permiso de trabajo" Urdu="کام کا اجازہ نامہ نہیں" Myanmar="အလုပ်လုပ်ခွင့်လိုင်စင်မရှိ" Pashto="د کار اجازه نشته">No Work Permit</option>
            <option value="Full-Time" English="Full-Time" Arabic="دوام كامل" Farsi="تمام وقت" Spanish="Tiempo completo" Urdu="فل ٹائم" Myanmar="အချိန်ပြည့်" Pashto="بشپړ وخت">Full-Time</option>
            <option value="Part-Time" English="Part-Time" Arabic="دوام جزئي" Farsi="پاره وقت" Spanish="Medio tiempo" Urdu="پارٹ ٹائم" Myanmar="အချိန်ပိုင်း" Pashto="نیمه وخت">Part-Time</option>
            <option value="Looking" English="Looking for work" Arabic="يبحث عن عمل" Farsi="به دنبال کار" Spanish="Buscando trabajo" Urdu="کام کی تلاش" Myanmar="အလုပ်ရှာနေသည်" Pashto="د کار په لټه کې">Looking for work</option>
            <option value="Disability" English="I have disability" Arabic="لدي إعاقة" Farsi="من معلولیت دارم" Spanish="Tengo una discapacidad" Urdu="میں معذور ہوں" Myanmar="ကျွန်ုပ်မသန်စွမ်းပါ" Pashto="زه معلولیت لرم">I have disability</option>
            <option value="Self-Employed" English="Self-Employed" Arabic="يعمل لحسابه الخاص" Farsi="خویش فرما" Spanish="Trabajador por cuenta propia" Urdu="خود روزگار" Myanmar="ကိုယ်ပိုင်လုပ်ငန်းရှင်" Pashto="په خپله کار کونکی">Self-Employed</option>
        </select>
        <hr>
        <label for="personal_status" English="Personal Status" Arabic="حالة شخصي" Farsi="شخصی وضعیت" Spanish="Estado personal" Urdu="شخصی حالت" Myanmar="အမေရိကန်းအခြေအနေ" Pashto="د شخصی حالت">Personal Status:</label>
        <select id="personal_status" name="personal_status" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="Married" English="Married" Arabic="متزوج" Farsi="متاهل" Spanish="Casado/a" Urdu="شادی شدہ" Myanmar="လက်ထပ်ထားသူ" Pashto="واده شوی">Married</option>
            <option value="Single" English="Single" Arabic="أعزب" Farsi="مجرد" Spanish="Soltero/a" Urdu="کنوارا" Myanmar="လူပျို" Pashto="واده نه شوی">Single</option>
            <option value="Divorced" English="Divorced" Arabic="مطلق" Farsi="مطلقه" Spanish="Divorciado/a" Urdu="طلاق یافتہ" Myanmar="ကွာရှင်းထားသူ" Pashto="چپل شوی">Divorced</option>
            <option value="Widowed" English="Widowed" Arabic="أرمل" Farsi="بیوه" Spanish="Viudo/a" Urdu="بیوہ" Myanmar="မုဆိုး/မုဆိုးမ" Pashto="بیوګین">Widowed</option>
            <option value="Separated" English="Separated" Arabic="منفصل" Farsi="جدا شده" Spanish="Separado/a" Urdu="علیحدہ" Myanmar="ခွဲခွာနေသူ" Pashto="جلا شوی">Separated</option>
        </select>
        <hr>

        <label for="spouse_name" English="Spouse Name" Arabic="اسم الزوج/الزوجة" Farsi="نام همسر" Spanish="Nombre del cónyuge" Urdu="شریک حیات کا نام" Myanmar="အိမ်ထောင်ဖက်အမည်" Pashto="د ګډون نوم">Spouse Name:</label>
        <input type="text" id="spouse_name" name="spouse_name" placeholder="Enter spouse's name">
        <hr>

        <!-- Spouse Work Status -->
        <label for="spouse_work" English="Spouse Work Status" Arabic="حالة عمل الزوج/الزوجة" Farsi="وضعیت کاری همسر" Spanish="Estado laboral del cónyuge" Urdu="شریک حیات کی کام کی حالت" Myanmar="အိမ်ထောင်ဖက်၏အလုပ်အခြေအနေ" Pashto="د ګډون د کار حالت">Spouse Work Status:</label>
        <select id="spouse_work" name="spouse_work">
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="Single" English="Single" Arabic="أعزب" Farsi="مجرد" Spanish="Soltero/a" Urdu="کنوارا" Myanmar="လူပျို" Pashto="واده نه شوی">Single</option>
            <option value="No Permit" English="No Work Permit" Arabic="لا يوجد تصريح عمل" Farsi="بدون مجوز کار" Spanish="Sin permiso de trabajo" Urdu="کام کا اجازہ نامہ نہیں" Myanmar="အလုပ်လုပ်ခွင့်လိုင်စင်မရှိ" Pashto="د کار اجازه نشته">No Work Permit</option>
            <option value="Full-Time" English="Full-Time" Arabic="دوام كامل" Farsi="تمام وقت" Spanish="Tiempo completo" Urdu="فل ٹائم" Myanmar="အချိန်ပြည့်" Pashto="بشپړ وخت">Full-Time</option>
            <option value="Part-Time" English="Part-Time" Arabic="دوام جزئي" Farsi="پاره وقت" Spanish="Medio tiempo" Urdu="پارٹ ٹائم" Myanmar="အချိန်ပိုင်း" Pashto="نیمه وخت">Part-Time</option>
            <option value="Looking" English="Looking for work" Arabic="يبحث عن عمل" Farsi="به دنبال کار" Spanish="Buscando trabajo" Urdu="کام کی تلاش" Myanmar="အလုပ်ရှာနေသည်" Pashto="د کار په لټه کې">Looking for work</option>
            <option value="Disability" English="I have disability" Arabic="لدي إعاقة" Farsi="من معلولیت دارم" Spanish="Tengo una discapacidad" Urdu="میں معذور ہوں" Myanmar="ကျွန်ုပ်မသန်စွမ်းပါ" Pashto="زه معلولیت لرم">I have disability</option>
            <option value="Self-Employed" English="Self-Employed" Arabic="يعمل لحسابه الخاص" Farsi="خویش فرما" Spanish="Trabajador por cuenta propia" Urdu="خود روزگار" Myanmar="ကိုယ်ပိုင်လုပ်ငန်းရှင်" Pashto="په خپله کار کونکی">Self-Employed</option>
        </select>
        <hr>

        <label for="spouse_age" English="Spouse Age" Arabic="عمر الزوج/الزوجة" Farsi="سن همسر" Spanish="Edad del cónyuge" Urdu="شریک حیات کی عمر" Myanmar="အိမ်ထောင်ဖက်အသက်" Pashto="د ګډون عمر">Spouse Age:</label>
        <input type="number" id="spouse_age" name="spouse_age" min="0" max="120" placeholder="Enter spouse's age">
        <hr>
        

        <label for="food_stamps" English="Food Stamps" Arabic="قسائم الطعام" Farsi="تمبر غذا" Spanish="Cupones de alimentos" Urdu="فوڈ اسٹیمپ" Myanmar="အစားအစာကူညီမှု" Pashto="د خوړو ټاپې">Food Stamps:</label>
        <select id="food_stamps" name="food_stamps" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="All Family Members" English="All Family Members" Arabic="جميع أفراد الأسرة" Farsi="تمام اعضای خانواده" Spanish="Todos los miembros de la familia" Urdu="خاندان کے تمام افراد" Myanmar="မိသားစုဝင်အားလုံး" Pashto="د کورنۍ ټول غړي">All Family Members</option>
            <option value="Some Family Members" English="Some Family Members" Arabic="بعض أفراد الأسرة" Farsi="برخی از اعضای خانواده" Spanish="Algunos miembros de la familia" Urdu="خاندان کے کچھ افراد" Myanmar="မိသားစုဝင်အချို့" Pashto="د کورنۍ ځینې غړي">Some Family Members</option>
            <option value="Not Yet" English="Not Yet" Arabic="ليس بعد" Farsi="هنوز نه" Spanish="Aún no" Urdu="ابھی نہیں" Myanmar="မရသေးပါ" Pashto="تر اوسه نه">Not Yet</option>
            <option value="Not Eligible" English="Not Eligible" Arabic="غير مؤهل" Farsi="واجد شرایط نیست" Spanish="No elegible" Urdu="اہل نہیں" Myanmar="အရည်အချင်းမပြည့်မီ" Pashto="وړتيا نلري">Not Eligible</option>
        </select>
        <hr>
        <label for="health_insurance" English="Health Insurance" Arabic="التأمين الصحي" Farsi="بیمه سلامت" Spanish="Seguro de salud" Urdu="صحت بیمہ" Myanmar="ကျန်းမာရေးအာမခံ" Pashto="د روغتیا بیمه">Health Insurance:</label>
        <select id="health_insurance" name="health_insurance" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="All Family Members" English="All Family Members" Arabic="جميع أفراد الأسرة" Farsi="تمام اعضای خانواده" Spanish="Todos los miembros de la familia" Urdu="خاندان کے تمام افراد" Myanmar="မိသားစုဝင်အားလုံး" Pashto="د کورنۍ ټول غړي">All Family Members</option>
            <option value="Some Family Members" English="Some Family Members" Arabic="بعض أفراد الأسرة" Farsi="برخی از اعضای خانواده" Spanish="Algunos miembros de la familia" Urdu="خاندان کے کچھ افراد" Myanmar="မိသားစုဝင်အချို့" Pashto="د کورنۍ ځینې غړي">Some Family Members</option>
            <option value="Not Yet" English="Not Yet" Arabic="ليس بعد" Farsi="هنوز نه" Spanish="Aún no" Urdu="ابھی نہیں" Myanmar="မရသေးပါ" Pashto="تر اوسه نه">Not Yet</option>
            <option value="Not Eligible" English="Not Eligible" Arabic="غير مؤهل" Farsi="واجد شرایط نیست" Spanish="No elegible" Urdu="اہل نہیں" Myanmar="အရည်အချင်းမပြည့်မီ" Pashto="وړتيا نلري">Not Eligible</option>
            <option value="Medicare" English="Medicare" Arabic="الرعاية الطبية" Farsi="مدیکر" Spanish="Medicare" Urdu="میڈیکیئر" Myanmar="မက်ဒီကာ" Pashto="مېډیکر">Medicare</option>
            <option value="Cannot Afford" English="Cannot Afford" Arabic="غير قادر على الدفع" Farsi="توانایی پرداخت ندارم" Spanish="No puedo pagar" Urdu="متحمل نہیں ہو سکتا" Myanmar="မတတ်နိုင်ပါ" Pashto="پرداخت نشي کولی">Cannot Afford</option>
        </select>
        <hr>
        <label for="householder_name" English="Full Name of the Head of the Household" Arabic="الاسم الكامل لرب الاسرة" Farsi="نام کامل سرپرست خانوار" Spanish="Nombre completo del cabeza de familia" Urdu="گھر کے سربراہ کا پورا نام" Myanmar="အိမ်ထောင်ရှင်အမည်" Pashto="د کور د مشر بشپړ نوم">Full Name of the Head of the Household:</label>
        <input type="text" id="householder_name" name="householder_name" required>
        <hr>      

                <!-- Address -->
        <label for="address" English="Address" Arabic="العنوان" Farsi="آدرس" Spanish="Dirección" Urdu="پتہ" Myanmar="လိပ်စာ" Pashto="پته">Address:</label>
        <input type="text" id="address" class="addressInputed" name="address" placeholder="e.g., 123 Main St" required autocomplete="off">
        <div id="suggestions"></div>
        <style>
            #suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            width: 80%;
            display: none;
            position: absolute;
            background-color: white;
            z-index: 1;
            }
            .suggestion-item {
            padding: 8px;
            cursor: pointer;
            }
            .suggestion-item:hover {
            background-color: #f0f0f0;
            }
        </style>
        <script>
            const addressInputed = document.querySelector('.addressInputed');
            const suggestionsDiv = document.getElementById('suggestions');

            addressInputed.addEventListener('input', function () {
                const query = addressInputed.value.trim();
                if (query.length > 2) { // Only fetch suggestions after 3 characters
                    debounceTimer = setTimeout(() => {
                    fetchSuggestions(query); // Call the API after 1 second
                    }, 1000); // 1-second delay
                } else {
                    suggestionsDiv.style.display = 'none'; // Hide suggestions if the input is too short
                }
            });

            function fetchSuggestions(query) {
                //fitches autocorrect location in dfw only!
                const apiKey = 'MAPBOX_API_KEY'; // Replace with your Mapbox API key
                const proximity = '-96.7970,32.7767'; // Longitude, Latitude for Dallas, TX for faster results
                const apiUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?proximity=${proximity}&access_token=${apiKey}`;         
                fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.features.length > 0) {
                    showSuggestions(data.features);
                    } else {
                    suggestionsDiv.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                });
            }

            function showSuggestions(suggestions) {
                suggestionsDiv.innerHTML = ''; // Clear previous suggestions
                suggestions.forEach(item => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.className = 'suggestion-item';
                    suggestionItem.textContent = item.place_name;
                    console.log(item);
                    
                    suggestionItem.addEventListener('click', () => {
                        // const addressOnly = item.place_name.split(',')[0] + ', ' + item.place_name.split(',')[1];
                        const addressOnly = item.place_name.split(',')[0];
                        addressInputed.value = addressOnly; // Set the selected address
                        suggestionsDiv.style.display = 'none'; // Hide suggestions
                    });
                    suggestionsDiv.appendChild(suggestionItem);
                });
                suggestionsDiv.style.display = 'block'; // Show suggestions
            }

            // Hide suggestions when clicking outside
            document.addEventListener('click', function (event) {
                if (event.target !== addressInputed) {
                suggestionsDiv.style.display = 'none';
                }
            });
        </script>
        <hr>

        <!-- Apartment Number (Optional) -->
        <label for="apt_num" English="Apartment Number (Optional)" Arabic="رقم الشقة (اختياري)" Farsi="شماره آپارتمان (اختیاری)" Spanish="Número de apartamento (Opcional)" Urdu="اپارٹمنٹ نمبر (اختیاری)" Myanmar="တိုက်ခန်းနံပါတ် (ရွေးချယ်နိုင်သည်)" Pashto="د اپارتمان نمبر (اختیاري)">Apartment Number (Optional):</label>
        <input type="text" id="apt_num" name="apt_num" placeholder="e.g., Apt 456">
        <hr>

        <!-- Gate Code (Optional) -->
        <label for="gate_code" English="Gate Code (Optional)" Arabic="رمز البوابة (اختياري)" Farsi="کد دروازه (اختیاری)" Spanish="Código de la puerta (Opcional)" Urdu="گیٹ کوڈ (اختیاری)" Myanmar="ဂိတ်ကုဒ် (ရွေးချယ်နိုင်သည်)" Pashto="د دروازې کوډ (اختیاري)">Gate Code (Optional):</label>
        <input type="text" id="gate_code" name="gate_code" placeholder="e.g., #1234">
        <hr>

        <!-- Complex Name (Optional) -->
        <label for="complex_name" English="Complex Name (Optional)" Arabic="اسم المجمع (اختياري)" Farsi="نام مجتمع (اختیاری)" Spanish="Nombre del complejo (Opcional)" Urdu="کمپلیکس کا نام (اختیاری)" Myanmar="ကွန်ပလက်အမည် (ရွေးချယ်နိုင်သည်)" Pashto="د کمپلېکس نوم (اختیاري)">Complex Name (Optional):</label>
        <input type="text" id="complex_name" name="complex_name" placeholder="e.g., Sunshine Apartments">
        <hr>

        <!-- City -->
        <label for="city" English="City" Arabic="المدينة" Farsi="شهر" Spanish="Ciudad" Urdu="شہر" Myanmar="မြို့" Pashto="ښار">City:</label>
        <!-- <input type="text" id="city" name="city" placeholder="e.g., Garland" required> -->
        <select name="city" id="city">
            <option value="Dallas">Dallas</option>
            <option value="Fort Worth">Fort Worth</option>
            <option value="Arlington">Arlington</option>
            <option value="Plano">Plano</option>
            <option value="Irving">Irving</option>
            <option value="Garland">Garland</option>
            <option value="Frisco">Frisco</option>
            <option value="McKinney">McKinney</option>
            <option value="Denton">Denton</option>
            <option value="Lewisville">Lewisville</option>
            <option value="Carrollton">Carrollton</option>
            <option value="Richardson">Richardson</option>
            <option value="Mesquite">Mesquite</option>
            <option value="Grand Prairie">Grand Prairie</option>
            <option value="Addison">Addison</option>
            <option value="The Colony">The Colony</option>
            <option value="Flower Mound">Flower Mound</option>
            <option value="Allen">Allen</option>
            <option value="Rowlett">Rowlett</option>
        </select>
        <hr>
        <!-- Zip Code -->
        <label for="zip_code" English="Zip Code" Arabic="الرمز البريدي" Farsi="کد پستی" Spanish="Código postal" Urdu="زپ کوڈ" Myanmar="စာတိုက်သင်္ကေတ" Pashto="د زپ کوډ">Zip Code:</label>
        <input type="number" id="zip_code" name="zip_code" placeholder="e.g., 90210" required>
        <hr>
        
        <label for="hotel_info" English="If you live in a Hotel, please write the Name of the Hotel, address, and the room number" Arabic="في حال وجودك في الفندق، يرجى كتابة اسم الفندق وعنوانه ورقم الغرفة" Farsi="اگر در هتل زندگی می کنید، لطفاً نام هتل، آدرس و شماره اتاق را بنویسید" Spanish="Si vives en un hotel, escribe el nombre del hotel, dirección y número de habitación" Urdu="اگر آپ ہوٹل میں رہتے ہیں، تو براہ کرم ہوٹل کا نام، پتہ اور کمرے کا نمبر لکھیں" Myanmar="ဟိုတယ်တွင်နေထိုင်ပါက၊ ဟိုတယ်အမည်၊ လိပ်စာနှင့်အခန်းနံပါတ်ကိုရေးပါ" Pashto="که تاسو په هوټل کې اوسئ، مهرباني وکړئ د هوټل نوم، پته او د خونې شمیره ولیکئ">If you live in a Hotel, please write the Name of the Hotel, address, and the room number:</label>
        <input type="text" id="hotel_info" name="hotel_info">
        <hr>

        <label for="country" English="Citizenship" Arabic="الجنسية" Farsi="تابعیت" Spanish="Ciudadanía" Urdu="شہریت" Myanmar="နိုင်ငံသား" Pashto="تابعیت">Citizenship:</label>        
        <select id="country" name="country" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <!-- Arab Countries -->
            <optgroup label="Arab Countries">
                <option value="DZ">Algeria | الجزائر</option>
                <option value="BH">Bahrain | البحرين</option>
                <option value="KM">Comoros | جزر القمر</option>
                <option value="DJ">Djibouti | جيبوتي</option>
                <option value="EG">Egypt | مصر</option>
                <option value="IQ">Iraq | العراق</option>
                <option value="JO">Jordan | الأردن</option>
                <option value="KW">Kuwait | الكويت</option>
                <option value="LB">Lebanon | لبنان</option>
                <option value="LY">Libya | ليبيا</option>
                <option value="MR">Mauritania | موريتانيا</option>
                <option value="MA">Morocco | المغرب</option>
                <option value="OM">Oman | عمان</option>
                <option value="PS">Palestine | فلسطين</option>
                <option value="QA">Qatar | قطر</option>
                <option value="SA">Saudi Arabia | السعودية</option>
                <option value="SO">Somalia | الصومال</option>
                <option value="SD">Sudan | السودان</option>
                <option value="SY">Syria | سوريا</option>
                <option value="TN">Tunisia | تونس</option>
                <option value="AE">United Arab Emirates | الإمارات العربية المتحدة</option>
                <option value="YE">Yemen | اليمن</option>
            </optgroup>

            <!-- All Other Countries -->
            <optgroup label="Other Countries">
                <option value="AF">Afghanistan | أفغانستان</option>
                <option value="AL">Albania | ألبانيا</option>
                <option value="AR">Argentina | الأرجنتين</option>
                <option value="AM">Armenia | أرمينيا</option>
                <option value="AU">Australia | أستراليا</option>
                <option value="AT">Austria | النمسا</option>
                <option value="AZ">Azerbaijan | أذربيجان</option>
                <option value="BD">Bangladesh | بنغلاديش</option>
                <option value="BY">Belarus | بيلاروسيا</option>
                <option value="BE">Belgium | بلجيكا</option>
                <option value="BA">Bosnia and Herzegovina | البوسنة والهرسك</option>
                <option value="BR">Brazil | البرازيل</option>
                <option value="BG">Bulgaria | بلغاريا</option>
                <option value="CM">Cameroon | الكاميرون</option>
                <option value="CA">Canada | كندا</option>
                <option value="CL">Chile | تشيلي</option>
                <option value="CN">China | الصين</option>
                <option value="CO">Colombia | كولومبيا</option>
                <option value="CD">Congo (Democratic Republic) | الكونغو (جمهورية الكونغو الديمقراطية)</option>
                <option value="CR">Costa Rica | كوستاريكا</option>
                <option value="CI">Côte d'Ivoire | ساحل العاج</option>
                <option value="HR">Croatia | كرواتيا</option>
                <option value="CU">Cuba | كوبا</option>
                <option value="CZ">Czech Republic | التشيك</option>
                <option value="DK">Denmark | الدنمارك</option>
                <option value="DO">Dominican Republic | جمهورية الدومينيكان</option>
                <option value="EC">Ecuador | الإكوادور</option>
                <option value="SV">El Salvador | السلفادور</option>
                <option value="ER">Eritrea | إريتريا</option>
                <option value="EE">Estonia | إستونيا</option>
                <option value="ET">Ethiopia | إثيوبيا</option>
                <option value="FI">Finland | فنلندا</option>
                <option value="FR">France | فرنسا</option>
                <option value="GA">Gabon | الغابون</option>
                <option value="GM">Gambia | غامبيا</option>
                <option value="GE">Georgia | جورجيا</option>
                <option value="DE">Germany | ألمانيا</option>
                <option value="GH">Ghana | غانا</option>
                <option value="GR">Greece | اليونان</option>
                <option value="GT">Guatemala | غواتيمالا</option>
                <option value="GN">Guinea | غينيا</option>
                <option value="HT">Haiti | هايتي</option>
                <option value="HN">Honduras | هندوراس</option>
                <option value="HU">Hungary | المجر</option>
                <option value="IN">India | الهند</option>
                <option value="ID">Indonesia | إندونيسيا</option>
                <option value="IR">Iran | إيران</option>
                <option value="IE">Ireland | أيرلندا</option>
                <option value="IT">Italy | إيطاليا</option>
                <option value="JM">Jamaica | جامايكا</option>
                <option value="JP">Japan | اليابان</option>
                <option value="KZ">Kazakhstan | كازاخستان</option>
                <option value="KE">Kenya | كينيا</option>
                <option value="XK">Kosovo | كوسوفو</option>
                <option value="KG">Kyrgyzstan | قيرغيزستان</option>
                <option value="LA">Laos | لاوس</option>
                <option value="LV">Latvia | لاتفيا</option>
                <option value="LR">Liberia | ليبيريا</option>
                <option value="LT">Lithuania | ليتوانيا</option>
                <option value="LU">Luxembourg | لوكسمبورغ</option>
                <option value="MK">North Macedonia | مقدونيا الشمالية</option>
                <option value="MY">Malaysia | ماليزيا</option>
                <option value="MV">Maldives | جزر المالديف</option>
                <option value="ML">Mali | مالي</option>
                <option value="MT">Malta | مالطا</option>
                <option value="MX">Mexico | المكسيك</option>
                <option value="MD">Moldova | مولدوفا</option>
                <option value="MM">Myanmar | ميانمار</option>
                <option value="NP">Nepal | نيبال</option>
                <option value="NL">Netherlands | هولندا</option>
                <option value="NZ">New Zealand | نيوزيلندا</option>
                <option value="NI">Nicaragua | نيكاراغوا</option>
                <option value="NE">Niger | النيجر</option>
                <option value="NG">Nigeria | نيجيريا</option>
                <option value="KP">North Korea | كوريا الشمالية</option>
                <option value="NO">Norway | النرويج</option>
                <option value="PK">Pakistan | باكستان</option>
                <option value="PA">Panama | بنما</option>
                <option value="PY">Paraguay | باراغواي</option>
                <option value="PE">Peru | بيرو</option>
                <option value="PH">Philippines | الفلبين</option>
                <option value="PL">Poland | بولندا</option>
                <option value="PT">Portugal | البرتغال</option>
                <option value="RO">Romania | رومانيا</option>
                <option value="RU">Russia | روسيا</option>
                <option value="SN">Senegal | السنغال</option>
                <option value="RS">Serbia | صربيا</option>
                <option value="SL">Sierra Leone | سيراليون</option>
                <option value="SG">Singapore | سنغافورة</option>
                <option value="SK">Slovakia | سلوفاكيا</option>
                <option value="SI">Slovenia | سلوفينيا</option>
                <option value="ZA">South Africa | جنوب أفريقيا</option>
                <option value="KR">South Korea | كوريا الجنوبية</option>
                <option value="SS">South Sudan | جنوب السودان</option>
                <option value="ES">Spain | إسبانيا</option>
                <option value="LK">Sri Lanka | سريلانكا</option>
                <option value="SE">Sweden | السويد</option>
                <option value="CH">Switzerland | سويسرا</option>
                <option value="TW">Taiwan | تايوان</option>
                <option value="TJ">Tajikistan | طاجيكستان</option>
                <option value="TZ">Tanzania | تنزانيا</option>
                <option value="TH">Thailand | تايلاند</option>
                <option value="TR">Turkey | تركيا</option>
                <option value="TM">Turkmenistan | تركمانستان</option>
                <option value="UG">Uganda | أوغندا</option>
                <option value="UA">Ukraine | أوكرانيا</option>
                <option value="GB">United Kingdom | المملكة المتحدة</option>
                <option value="US">United States | الولايات المتحدة</option>
                <option value="UY">Uruguay | أوروغواي</option>
                <option value="UZ">Uzbekistan | أوزبكستان</option>
                <option value="VE">Venezuela | فنزويلا</option>
                <option value="VN">Vietnam | فيتنام</option>
                <option value="ZM">Zambia | زامبيا</option>
                <option value="ZW">Zimbabwe | زيمبابوي</option>
            </optgroup>
        </select>
        <hr>
        <label for="date_arrived" English="Date Arrived in the US" Arabic="تاريخ الوصول إلى الولايات المتحدة" Farsi="تاریخ ورود به ایالات متحده" Spanish="Fecha de llegada a EE.UU." Urdu="امریکہ میں آنے کی تاریخ" Myanmar="အမေရိကရောက်ရှိချိန်" Pashto="د امریکا ته د رسیدو نیټه">Date Arrived in the US:</label>
        <input type="date" id="date_arrived" name="date_arrived" required>
        <hr>
        <label for="can_text" English="Can we text you?" Arabic="هل يمكننا إرسال رسائل لك؟" Farsi="آیا می توانیم به شما پیامک بزنیم؟" Spanish="¿Podemos enviarte mensajes?" Urdu="کیا ہم آپ کو پیغامات بھیج سکتے ہیں؟" Myanmar="ကျွန်ုပ်တို့သည် သင်တို့အားစာတိုပို့နိုင်ပါသလား?" Pashto="ایا موږ تاسو ته متن کولای شو؟">Can we text you?</label>
        <select id="can_text" name="can_text" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل" required>Select</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>
        <hr>

        
        <label for="proxy_name" English="" Arabic="اسم الوكيل (إذا كنت تكمل الاستمارة لشخص آخر)" Farsi="نام وکیل (اگر فرم را برای شخص دیگری تکمیل می‌کنید)" Spanish="Nombre del representante (si está completando un formulario para otra persona)" Urdu="پراکسی کا نام (اگر آپ کسی اور کے لیے فارم مکمل کر رہے ہیں)" Myanmar="ကိုယ်စားပြုသူ၏အမည် (သင့်အတွက်မဟုတ်သောလူအတွက် ဖောင်ပြည့်စွက်ပါက)" Pashto="د استازي نوم (که د چا لپاره بڼه بشپړوي)">Proxy Name (if completing a form for someone else)</label>
        <input type="text" id="proxy_name" name="proxy_name">
        <hr>

        <label for="proxy_phone" English="" Arabic="رقم هاتف الوكيل (إذا كنت تكمل الاستمارة لشخص آخر)" Farsi="شماره تلفن وکیل (اگر فرم را برای شخص دیگری تکمیل می‌کنید)" Spanish="Número de teléfono del representante (si está completando un formulario para otra persona)" Urdu="پراکسی فون نمبر (اگر آپ کسی اور کے لیے فارم مکمل کر رہے ہیں)" Myanmar="ကိုယ်စားပြုသူ၏ဖုန်းနံပါတ် (သင့်အတွက်မဟုတ်သောလူအတွက် ဖောင်ပြည့်စွက်ပါက)" Pashto="د استازي د تلیفون شمېره (که د چا لپاره بڼه بشپړوي)">Proxy Phone Number (if completing a form for someone else)</label>
        <input type="text" id="proxy_phone" name="proxy_phone">
        <hr>

        <label for="nationality" English="Nationality" Arabic="الصفة القومية" Farsi="ملیت" Spanish="Nacionalidad" Urdu="قومیت" Myanmar="နိုင်ငံသား" Pashto="تابعیت">Nationality:</label>
        <select id="nationality" name="nationality" required>
            <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
            <option value="American Indian or Alaska Native" Arabic="أمريكا أصغر البيضاء" Farsi="آمریکایی مردم بین البیضا" Spanish="Indiano americano o alaskano" Urdu="امریکی مردم بین البیضاء" Myanmar="အမေရိကန်းမား အမေရိကန်းလ်တား" Pashto="آمریکایی مردم بین البیضا">American Indian or Alaska Native</option>
            <option value="Asian" Arabic="آسيايي" Farsi="آسیا" Spanish="Asiano" Urdu="آسیا" Myanmar="အမေရိကန်းလ်တား" Pashto="آسیا">Asian</option>
            <option value="Black or African American" Arabic="أسود أو أفريقيا" Farsi="سیاه و آفریقایی" Spanish="Negro o africano" Urdu="سیاہ یا آفریقی" Myanmar="အမေရိကန်းလ်တား" Pashto="سیاہ یا آفریقی">Black or African American</option>
            <option value="Hispanic or Latino" Arabic="هسباني أو لاتيني" Farsi="هسپانی یا لاتینی" Spanish="Hispano o latino" Urdu="ہسپانی یا لاطینی" Myanmar="အမေရိကန်းလ်တား" Pashto="هسپانی یا لاطینی">Hispanic or Latino</option>
            <option value="Native Hawaiian or Other Pacific Islander" Arabic="أمريكا أصغر البيضاء" Farsi="آمریکایی مردم بین البیضا" Spanish="Indiano americano o alaskano" Urdu="امریکی مردم بین البیضاء" Myanmar="အမေရိကန်းမား အမေရိကန်းလ်တား" Pashto="آمریکایی مردم بین البیضا">Native Hawaiian or Other Pacific Islander</option>
            <option value="White" Arabic="أبيض" Farsi="سیاه" Spanish="Blanco" Urdu="سیاہ" Myanmar="အမေရိကန်းလ်တား" Pashto="سیاہ">White</option>
        </select>
        <hr>
        
        
        <label for="num_adults" English="Number of Adults 18-59" Arabic="عدد أفراد البالغين ما بين عمر" Farsi="تعداد بزرگسالان 18-59" Spanish="Número de adultos 18-59" Urdu="بالغ افراد کی تعداد 18-59" Myanmar="လူကြီး 18-59 ဦးရေ" Pashto="د 18-59 کلونو عمر لرونکي بالغانو شمیر">Number of Adults 18-59:</label>
        <input type="number" id="num_adults" name="num_adults" min="0" required>
        
        <hr>
        <label for="num_seniors" English="Number of Seniors 60+" Arabic="عدد أفراد كبار السن" Farsi="تعداد سالمندان 60+" Spanish="Número de personas mayores 60+" Urdu="60+ بزرگ شہریوں کی تعداد" Myanmar="အငယ်တန်းကျောင်းသားနောက်" Pashto="د 60+ کلونو عمر لرونکي زاړه شمیر">Number of Seniors 60+:</label>
        <input type="number" id="num_seniors" name="num_seniors" min="0" required>
        <hr>

        <label for="num_children" English="Number of Children, Ages 0-17" Arabic="عدد أفراد الأطفال ما بين سن" Farsi="تعداد کودکان 0-17" Spanish="Número de niños, edades 0-17" Urdu="بچوں کی تعداد، عمر 0-17" Myanmar="ကလေးများရေအတွက်၊ အသက်အရွယ် 0-17" Pashto="د ماشومانو شمیر، عمرونه 0-17">Number of Children, Ages 0-17:</label>
        <input type="number" id="num_children" name="num_children" min="0" required>
        <hr>

        <div id="children-container" style="display:none;">
            <h3 style="text-align: center;" Arabic="إضافة معلومات الطفل" Farsi="افزودن اطلاعات کودک" Spanish="Agregar información del niño" Urdu="بچے کی معلومات شامل کریں" Myanmar="ကလေးသတင်းအချက်အလက်ထည့်ရန်" Pashto="د ماشوم معلومات اضافه کړئ">Add Child Information
            </h3>
        </div>

            <style>
                input[type="checkbox"]{
                height: 20px;
                margin-top: 15px;
                margin-right: 10px;
            }
            </style>
        <label for="aid_names" Arabic="اختر المساعدة الوطنية" Persian="انتخاب امکانات وطنی" Spanish="Seleccione la ayuda gubernamental" Urdu="انتخاب معلومات وطنی">Select Government Aid:</label>
        <br>
        <input type="checkbox" id="SNAP" name="aid_names[]" value="SNAP">
        <label for="SNAP">SNAP</label><br>
        <input type="checkbox" id="WIC" name="aid_names[]" value="WIC">
        <label for="WIC">WIC</label><br>
        <input type="checkbox" id="TANF" name="aid_names[]" value="TANF">
        <label for="TANF">TANF</label><br>
        <input type="checkbox" id="Unemployment" name="aid_names[]" value="Unemployment">
        <label for="Unemployment">Unemployment</label><br>
        <input type="checkbox" id="Medicaid" name="aid_names[]" value="Medicaid">
        <label for="Medicaid">Medicaid</label><br>
        <input type="checkbox" id="SSI" name="aid_names[]" value="SSI">
        <label for="SSI">SSI</label><br>
        <input type="checkbox" id="Free/Reduced School Lunches" name="aid_names[]" value="Free/Reduced School Lunches">
        <label for="Free/Reduced School Lunches">Free/Reduce</label><br>
        
        <hr>
        
        <label for="income" English="Total Household Income" Arabic="مجموع دخل الأسرة" Farsi="درآمد کل خانوار" Spanish="Ingreso total del hogar" Urdu="کل گھریلو آمدنی" Myanmar="စုစုပေါင်းအိမ်သုံးဝင်ငွေ" Pashto="ټول کورني عاید">Total Household Income:</label>
        <input type="number" id="income" name="income" required>
        <hr>
        
        <label for="income_per" English="The Income" Arabic="الدخل" Farsi="درآمد" Spanish="El ingreso" Urdu="آمدنی" Myanmar="ဝင်ငွေ" Pashto="عاید">The Income:</label>
        <select id="income_per" name="income_per" required>
            <option value="No Income" Arabic="لا يوجد دخل" Farsi="درآمد نیست" Spanish="No hay ingreso" Urdu="آمدنی نہیں ملی" Myanmar="ဝင်ငွေ မရှိပါ" Pashto="عاید نیست">No Income</option>
            <option value="Per Week" Arabic="كل أسبوع" Farsi="هر هفته" Spanish="Cada semana" Urdu="ہر ہفتہ" Myanmar="တွေ နှစ်" Pashto="هر هفته">Per Week</option>
            <option value="Per Month" Arabic="كل شهر" Farsi="هر ماه" Spanish="Cada mes" Urdu="ہر ماہ" Myanmar="တွေ နေ့" Pashto="هر ماه">Per Month</option>
            <option value="Per Year" Arabic="كل عام" Farsi="هر سال" Spanish="Cada año" Urdu="ہر سال" Myanmar="တွေ နှစ်" Pashto="هر سال">Per Year</option>
        </select>
        <hr>

        <label for="comment" English="Additional Comments" Arabic="تعليقات إضافية" Farsi="نظرات اضافی" Spanish="Comentarios adicionales" Urdu="اضافی تبصرے" Myanmar="ထပ်ဆင့်မှတ်ချက်များ" Pashto="اضافي تبصرې">Additional Comments:</label>
        <textarea id="comment" name="comment"></textarea>
        <hr>

        <a id="terms" style="text-align: center; display: block; margin: 0 0 0px 0 !important;" href="https://www.fns.usda.gov/tefap/participant-agreement-rights-obligations-and-fair-hearing-request" target="_blank">Link</a>
        <div class="label-input">
            <input type="radio" id="participant_agreement" name="participant_agreement" required>
            <label for="participant_agreement" English="I agree to the Participant Agreement, Rights, Obligations, and Fair Hearing Request and confirm that I have read and understood the terms." 
                Arabic="أوافق على اتفاقية المشارك، الحقوق، الالتزامات، وطلب جلسة استماع عادلة وأؤكد أنني قد قرأت وفهمت الشروط." 
                Farsi="من با توافق‌نامه شرکت‌کننده، حقوق، تعهدات و درخواست جلسه رسیدگی عادلانه موافقم و تأیید می‌کنم که شرایط را خوانده‌ام و درک کرده‌ام." 
                Spanish="Estoy de acuerdo con el Acuerdo del Participante, Derechos, Obligaciones y Solicitud de Audiencia Justa y confirmo que he leído y entendido los términos." 
                Urdu="میں شرکت کنندہ معاہدہ، حقوق، ذمہ داریوں اور منصفانہ سماعت کی درخواست سے متفق ہوں اور تصدیق کرتا ہوں کہ میں نے شرائط کو پڑھ اور سمجھ لیا ہے۔" 
                Myanmar="ကျွန်ုပ်သည် ပါဝင်သူသဘောတူညီချက်၊ အခွင့်အရေး၊ တာဝန်ဝတ္တရားများနှင့် မျှတသောနားထောင်မှုတောင်းဆိုချက်ကို သဘောတူပြီး စည်းကမ်းချက်များကို ဖတ်ရှုနားလည်ကြောင်း အတည်ပြုပါသည်။" 
                Pashto="زه د ګډون کوونکي د هوکړې، حقونو، دندو او منصفانه اوریدلو غوښتنې سره موافق یم او تایید کوم چې ما شرطونه لوستلي او پوه شوي یم.">
                I agree to the <a href="https://www.fns.usda.gov/tefap/participant-agreement-rights-obligations-and-fair-hearing-request" target="_blank">Participant Agreement, Rights, Obligations, and Fair Hearing Request</a> and confirm that I have read and understood the terms.
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
        

        <label  English="Signature (Type your full name)" Arabic="التوقيع (اكتب اسمك بالكامل)" Persian="امضا (نوع نامك بالكامل)" Spanish="Firma (Escribe tu nombre completo)" Urdu="التوقيع (نوع نامك بالكامل)" Myanmar="စည်းစုံတင်သည် (ပါးမှာကို ထည့်ရန်)" Pashto="امضا (نوع نامك بالكامل)">Signature (Type your full name):</label>
        <input type="text" id="" name="" required>
        <hr>
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <input id="submit" class="general-button" type="submit" value="Register" Arabic="تسجيل" Farsi="ثبت نام" Spanish="Registrar" Urdu="ثبت نام" Myanmar="အသစ်ထည့်ရန်" Pashto="سانډال">
    </form>

    <script>
        let childCount = 0;
        const input = document.getElementById('num_children');
        const container = document.getElementById('children-container');

        input.addEventListener('keyup', function() {
            container.innerHTML = '';
            childCount = 0;
            if (input.value > 0) {
                document.getElementById('children-container').style.display = 'block';
                for (let i = 0; i < input.value; i++) {
                    addChildFields();
                }
            }
        });
        
        function addChildFields() {
            childCount++;

           
            const childFields = `
                <fieldset>
                    <legend>Child ${childCount}</legend>
                    
                    <!-- Name -->
                    <label for="child_name_${childCount}" English="Name" Arabic="الاسم" Farsi="نام" Spanish="Nombre" Urdu="نام" Myanmar="နာမည်" Pashto="نوم">Name</label>
                    <input type="text" id="child_name_${childCount}" name="children[${childCount}][name]" required><hr>
                    
                    <!-- Gender -->
                    <label for="child_gender_${childCount}" English="Gender" Arabic="الجنس" Farsi="جنسیت" Spanish="Género" Urdu="جنس" Myanmar="လိင်" Pashto="جنس">Gender</label>
                    <select id="child_gender_${childCount}" name="children[${childCount}][gender]" required>
                        <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
                        <option value="Male" English="Male" Arabic="ذكر" Farsi="مرد" Spanish="Masculino" Urdu="مرد" Myanmar="ကျား" Pashto="نارینه">Male</option>
                        <option value="Female" English="Female" Arabic="أنثى" Farsi="زن" Spanish="Femenino" Urdu="عورت" Myanmar="မ" Pashto="ښځه">Female</option>
                    </select><hr>
                    
                    <!-- Age -->
                    <label for="child_age_${childCount}" English="Age" Arabic="العمر" Farsi="سن" Spanish="Edad" Urdu="عمر" Myanmar="အသက်" Pashto="عمر">Age</label>
                    <input type="number" id="child_age_${childCount}" name="children[${childCount}][age]" min="0" required><hr>
                    
                    <!-- School Status -->
                    <label for="school_status_${childCount}" English="Is the child currently going to school?" Arabic="هل الطفل يذهب إلى المدرسة حالياً؟" Farsi="آیا کودک در حال حاضر به مدرسه می‌رود؟" Spanish="¿El niño/a va actualmente a la escuela?" Urdu="کیا بچہ فی الحال اسکول جا رہا ہے؟" Myanmar="ကလေးသည် လက်ရှိတွင် ကျောင်းနေပါသလား။" Pashto="ایا ماشوم اوس مهال ښوونځي ته ځي؟">Is the child currently going to school?</label>
                    <select id="school_status_${childCount}" name="children[${childCount}][school_status]" required>
                        <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
                        <option value="Yes" English="Yes, the child is going to school." Arabic="نعم، الطفل يذهب إلى المدرسة." Farsi="بله، کودک به مدرسه می‌رود." Spanish="Sí, el niño/a va a la escuela." Urdu="ہاں، بچہ اسکول جا رہا ہے۔" Myanmar="ဟုတ်ကဲ့၊ ကလေးကျောင်းနေပါတယ်။" Pashto="هو، ماشوم ښوونځي ته ځي.">Yes</option>
                        <option value="No" English="No, the child is not going to school." Arabic="لا، الطفل لا يذهب إلى المدرسة." Farsi="خیر، کودک به مدرسه نمی‌رود." Spanish="No, el niño/a no va a la escuela." Urdu="نہیں، بچہ اسکول نہیں جا رہا ہے۔" Myanmar="မဟုတ်ပါ၊ ကလေးကျောင်းမနေပါ။" Pashto="نه، ماشوم ښوونځي ته نه ځي.">No</option>
                        <option value="Other" English="Other (e.g., homeschooled, dropped out)." Arabic="أخرى (مثل: تعليم منزلي، ترك المدرسة)." Farsi="دیگر (مثلاً: تحصیل در خانه، ترک تحصیل)." Spanish="Otro (por ejemplo, educación en casa, abandonó la escuela)." Urdu="دیگر (مثلاً: گھر پر تعلیم، اسکول چھوڑ دیا)۔" Myanmar="အခြား (ဥပမာ၊ အိမ်တွင်းပညာရေး၊ ကျောင်းထွက်)။" Pashto="نور (لکه: کورني زده کړه، ښوونځي پریښودل).">Other</option>
                    </select><hr>
                    
                    <!-- Job Status -->
                    <label for="job_status_${childCount}" English="Is the child currently working?" Arabic="هل الطفل يعمل حالياً؟" Farsi="آیا کودک در حال حاضر کار می‌کند؟" Spanish="¿El niño/a está trabajando actualmente?" Urdu="کیا بچہ فی الحال کام کر رہا ہے؟" Myanmar="ကလေးသည် လက်ရှိတွင် အလုပ်လုပ်နေပါသလား။" Pashto="ایا ماشوم اوس مهال کار کوي؟">Is the child currently working?</label>
                    <select id="job_status_${childCount}" name="children[${childCount}][job_status]" required>
                        <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
                        <option value="Yes" English="Yes, the child is working." Arabic="نعم، الطفل يعمل." Farsi="بله، کودک کار می‌کند." Spanish="Sí, el niño/a está trabajando." Urdu="ہاں، بچہ کام کر رہا ہے۔" Myanmar="ဟုတ်ကဲ့၊ ကလေးအလုပ်လုပ်နေပါတယ်။" Pashto="هو، ماشوم کار کوي.">Yes</option>
                        <option value="No" English="No, the child is not working." Arabic="لا، الطفل لا يعمل." Farsi="خیر، کودک کار نمی‌کند." Spanish="No, el niño/a no está trabajando." Urdu="نہیں، بچہ کام نہیں کر رہا ہے۔" Myanmar="မဟုတ်ပါ၊ ကလေးအလုပ်မလုပ်ပါ။" Pashto="نه، ماشوم کار نه کوي.">No</option>
                        <option value="Disability" English="The child has a disability and cannot work." Arabic="الطفل لديه إعاقة ولا يمكنه العمل." Farsi="کودک معلولیت دارد و نمی‌تواند کار کند." Spanish="El niño/a tiene una discapacidad y no puede trabajar." Urdu="بچہ معذور ہے اور کام نہیں کر سکتا۔" Myanmar="ကလေးမသန်စွမ်းပါ၊ အလုပ်မလုပ်နိုင်ပါ။" Pashto="ماشوم معلولیت لري او کار نشي کولی.">Disability</option>
                        <option value="No Permit" English="The child does not have a work permit." Arabic="الطفل ليس لديه تصريح عمل." Farsi="کودک مجوز کار ندارد." Spanish="El niño/a no tiene permiso de trabajo." Urdu="بچہ کے پاس کام کا اجازہ نامہ نہیں ہے۔" Myanmar="ကလေးတွင် အလုပ်လုပ်ရန် ခွင့်ပြုချက်မရှိပါ။" Pashto="ماشوم د کار اجازه نه لري.">No Permit</option>
                        <option value="Not Eligible" English="The child is not eligible to work." Arabic="الطفل غير مؤهل للعمل." Farsi="کودک واجد شرایط کار نیست." Spanish="El niño/a no es elegible para trabajar." Urdu="بچہ کام کرنے کے لئے اہل نہیں ہے۔" Myanmar="ကလေးသည် အလုပ်လုပ်ရန် အရည်အချင်းမပြည့်မီပါ။" Pashto="ماشوم د کار لپاره وړتيا نلري.">Not Eligible</option>
                    </select><hr>   
                    <!-- Has Disability -->
                    <label for="has_disability_${childCount}" English="Has Disability" Arabic="لديه إعاقة" Farsi="معلولیت دارد" Spanish="Tiene discapacidad" Urdu="معذوری ہے" Myanmar="မသန်စွမ်းမှုရှိသည်" Pashto="معلولیت لري">Has Disability</label>
                    <select id="has_disability_${childCount}" name="children[${childCount}][has_disability]" required>
                        <option value="" selected disabled hidden English="Select" Arabic="اختر" Farsi="انتخاب کنید" Spanish="Seleccionar" Urdu="منتخب کریں" Myanmar="ရွေးချယ်ပါ" Pashto="ټاکل">Select</option>
                        <option value="Yes" English="Yes" Arabic="نعم" Farsi="بله" Spanish="Sí" Urdu="ہاں" Myanmar="ဟုတ်ကဲ့" Pashto="هو">Yes</option>
                        <option value="No" English="No" Arabic="لا" Farsi="خیر" Spanish="No" Urdu="نہیں" Myanmar="မဟုတ်ပါ" Pashto="نه">No</option>
                    </select>
                </fieldset>
            `;
            
            container.insertAdjacentHTML('beforeend', childFields);
            container.appendChild(document.createElement('hr'));
            
            updateChildLanguage(document.getElementById('language').value);
            // Function to set the language for all elements inside <fieldset> elements
            function updateChildLanguage(lang) {
                // Select all elements inside <fieldset> elements
                const elementsInsideFieldsets = document.querySelectorAll('fieldset *');

                elementsInsideFieldsets.forEach(element => {
                    // Check if the element has the language attribute
                    if (element.hasAttribute(lang)) {
                        // Get the translation for the selected language
                        const translation = element.getAttribute(lang);

                        // Update the element's text content
                        element.textContent = translation;
                    }
                });
            }
        }

        function toggleOtherField(checkbox) {
            const otherField = document.getElementById('other_field');
            if (checkbox.checked) {
                otherField.style.display = 'block';
                otherField.required = true;
            } else {
                otherField.style.display = 'none';
                otherField.required = false;
            }
}

    function validateEnglish(input) {
      // Regular expression to allow only English letters, numbers, and basic punctuation
      const englishRegex = /^[A-Za-z0-9\s\.,!?;:'"()\-+@#$%^&*\/\\]+$/;
      return englishRegex.test(input);
    }

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
    for (const inputField of inputFields) {
        inputField.addEventListener("keyup", function(event) {
            if (!validateEnglish(event.target.value) && inputField.value.length > 0) {
                alert(translations['Please enter text in English only.'][language]);
                inputField.value = "";
                inputField.focus();
            }
        });
        
    }
    </script>

</body>
</html>
