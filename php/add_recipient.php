<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
include '../Admin/vendor/autoload.php';
use Twilio\Rest\Client;

// Normalize phone number
function normalize_phone_number($phone) {
    return preg_replace(['/^\+1|^1/', '/\)/', '/\(/', '/\s+/', '/\+/', '/-/', '/\s1/'], '', $phone);
}


// Data from POST request
$full_name = $_POST['full_name'];
$email = $_POST['email'] ?? null;
$address = $_POST['address'] ?? null;
$apt_num = empty($_POST['apt_num']) ? NULL : $_POST['apt_num'];
$comp_name = empty($_POST['complex_name']) ? NULL : $_POST['complex_name'];
$gate_code = empty($_POST['gate_code']) ? NULL : $_POST['gate_code'];
$city = $_POST['city'] ?? null;
$zip_code = $_POST['zip_code'] ?? null;
$hotel_info = empty($_POST['hotel_info']) ? NULL : $_POST['hotel_info'];
$phone = normalize_phone_number($_POST['phone'] ?? '');
$textable = ($_POST['can_text'] ?? '') == "Yes" ? 1 : 0;
$language = $_POST['language'] ?? null;
$english = $_POST['english'] ?? null;
$num_adults = empty($_POST['num_adults']) ? 0 : (int)$_POST['num_adults'];
$num_children = empty($_POST['num_children']) ? 0 : (int)$_POST['num_children'];
$num_seniors = empty($_POST['num_seniors']) ? 0 : (int)$_POST['num_seniors'];
$latitude = empty($_POST['latitude']) ? NULL : ((float)$_POST['latitude']);
$longitude = empty($_POST['longitude']) ? NULL : ((float)$_POST['longitude']);

$replied = 'No response'; // Default

// Prepare the SQL query
$sql_recipient = "INSERT INTO recipient (
    full_name, email, phone, textable, language, english, address, apt_num, 
    city, zip_code, hotel_info, num_adults, num_children, num_seniors, 
    latitude, longitude, replied, comp_name, gate_code
) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?)";

$stmt_recipient = $conn->prepare($sql_recipient);

// Bind parameters
$stmt_recipient->bind_param(
    'sssisssssisiiiddsss',  // Adjusted type string for integers, floats, and strings
    $full_name, $email, $phone, $textable, $language, $english, 
    $address, $apt_num, $city, $zip_code, $hotel_info, $num_adults, 
    $num_children, $num_seniors, $latitude, $longitude, $replied, $comp_name, $gate_code
);



if ($stmt_recipient->execute()) {
    $recipient_id = $stmt_recipient->insert_id;

    // Insert into recipient_details table
    $gender = $_POST['gender'];
    $householder_name = $_POST['householder_name'];
    $date_arrived = $_POST['date_arrived'];
    $personal_status = $_POST['personal_status'];
    $work_status = $_POST['work_status'];
    $nationality = $_POST['nationality'];
    $income = empty($_POST['income']) ? 0 : $_POST['income'];
    $income_per = $_POST['income_per'];
    $spouse_name = $_POST['spouse_name'] ?? NULL;
    $spouse_work = $_POST['spouse_work'] ?? NULL;
    $spouse_age = $_POST['spouse_age'] ?? NULL;

    $selectedAids = $_POST['aid_names'] ?? [];
    // //convert array to tring
    $gov_aid = implode(", ", $selectedAids);    
    
    $food_stamps = $_POST['food_stamps'];
    $health_insurance = $_POST['health_insurance'];
    $comment = empty($_POST['comment']) ? NULL : $_POST['comment'];
    $proxy_name = empty($_POST['proxy_name']) ? NULL : $_POST['proxy_name'];
    $proxy_phone = empty($_POST['proxy_phone']) ? NULL : $_POST['proxy_phone'];
    $age = empty($_POST['age']) ? NULL : $_POST['age'];
    $country = empty($_POST['country']) ? NULL : $_POST['country'];
    $sql_details = "INSERT INTO recipient_details (recipient_id, gender, householder_name, date_arrived, work_status, nationality, income, income_per, gov_aid, food_stamps, health_insurance, comment, proxy_name, proxy_phone, personal_status, age, country, spouse_name, spouse_work, spouse_age)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param(
        'isssssissssssssisssi',
        $recipient_id, $gender, $householder_name, $date_arrived, $work_status, $nationality, 
        $income, $income_per, $gov_aid, $food_stamps, $health_insurance, $comment, 
        $proxy_name, $proxy_phone, $personal_status, $age, $country, $spouse_name, $spouse_work, $spouse_age
    );
    
    $stmt_details->execute();

    // Insert children data (if any)
    if (!empty($_POST['children'])) {
        $sql_child = "INSERT INTO recipient_children (recipient_id, name, gender, age, school_status, job_status, has_disability) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_child = $conn->prepare($sql_child);

        foreach ($_POST['children'] as $child) {
            $child_name = $child['name'];
            $child_gender = $child['gender'];
            $child_age = $child['age'];
            $school_status = $child['school_status'];
            $job_status = $child['job_status'];
            $has_disability = ($child['has_disability'] == "Yes") ? 1 : 0;

            $stmt_child->bind_param(
                'ississi', 
                $recipient_id, $child_name, $child_gender, $child_age, 
                $school_status, $job_status, $has_disability
            );
            $stmt_child->execute();
        }
    }

    $account_sid = 'TWILIO_ACCOUNT_SID'; // Replace with your Twilio account SID
    $auth_token = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio auth token
    $twilio_number = 'TWILIO_RECIPIENT_PHONE_NUMBER'; // Replace with your Twilio number

    $name = $full_name;
    $client = new Client($account_sid, $auth_token);
    $message = "Dear $name,\n\nThank you for registering with American Islamic Diversity (AID).\n\nPlease save this number in your contacts: +1 (469) 960-4655, so we can notify you about upcoming food pantry deliveries.\n\nWe look forward to serving you!\n\nThank you!";

    if ($language == 'Arabic') {
        $message = "عزيزي/عزيزتي $name،\n\nشكرًا لتسجيلك في التنوع الإسلامي الأمريكي (AID).\n\nيرجى حفظ هذا الرقم في جهات اتصالك: +1 (469) 960-4655، حتى نتمكن من إعلامك بتسليمات بنك الطعام القادمة.\n\nنتطلع إلى خدمتكم!\n\nشكرًا!";
    } else if ($language == 'Farsi') {
        $message = "عزیز $name،\n\nاز ثبت نام شما در تنوع اسلامی آمریکایی (AID) متشکریم.\n\nلطفاً این شماره را در مخاطبین خود ذخیره کنید: +1 (469) 960-4655، تا بتوانیم شما را از تحویل‌های آینده بانک غذا مطلع کنیم.\n\nما مشتاق خدمت به شما هستیم!\n\nمتشکرم!";
    } else if ($language == 'Spanish') {
        $message = "Estimado/a $name,\n\nGracias por registrarse en American Islamic Diversity (AID).\n\nPor favor, guarde este número en sus contactos: +1 (469) 960-4655, para que podamos notificarle sobre próximas entregas de despensas de alimentos.\n\n¡Esperamos poder servirle!\n\n¡Gracias!";
    } else if ($language == 'Urdu') {
        $message = "محترم $name،\n\nامریکن اسلامی ڈائیورسٹی (AID) میں رجسٹر کرنے کا شکریہ۔\n\nبراہ کرم اس نمبر کو اپنے کانٹیکٹس میں محفوظ کریں: +1 (469) 960-4655، تاکہ ہم آپ کو آنے والی فوڈ پینٹری ڈیلیوری کے بارے میں مطلع کر سکیں۔\n\nہم آپ کی خدمت کرنے کے منتظر ہیں!\n\nشکریہ!";
    } else if ($language == 'Myanmar') {
        $message = "ချစ်စရာ $name၊\n\nAmerican Islamic Diversity (AID) တွင် မှတ်ပုံတင်သည့်အတွက် ကျေးဇူးတင်ပါသည်။\n\nကျေးဇူးပြု၍ ကျွန်ုပ်တို့၏ဖုန်းနံပါတ်ကို သင့်ဖုန်းစာရင်းတွင် သိမ်းဆည်းထားပါ: +1 (469) 960-4655၊ သို့မှသာ ကျွန်ုပ်တို့သည် သင့်အား လာမည့်အစားအစာဖြန့်ဝေမှုများအကြောင်း အသိပေးနိုင်မည်ဖြစ်သည်။\n\nသင့်ကို ဝန်ဆောင်မှုပေးရန် ကျွန်ုပ်တို့ မျှော်လင့်ပါသည်!\n\nကျေးဇူးတင်ပါသည်!";
    } else if ($language == 'Pashto') {
        $message = "ګرانه $name،\n\nد امریکایی اسلامي تنوع (AID) سره د ثبت نام لپاره مننه.\n\nمهرباني وکړئ دا شمیره خپل اړیکو کې خوندي کړئ: +1 (469) 960-4655، ترڅو موږ تاسو ته د راتلونکو خواړه د پانټري تحویلو په اړه خبر ورکړو.\n\nموږ ستاسو د خدمت لپاره هیښیار یو!\n\nمننه!";
    } else if ($language == 'Other') {
        $message = "Dear $name,\n\nThank you for registering with American Islamic Diversity (AID).\n\nPlease save this number in your contacts: +1 (469) 960-4655, so we can notify you about upcoming food pantry deliveries.\n\nWe look forward to serving you!\n\nThank you!";
    }

    try {
        $client->messages->create(
            $phone,
            ['from' => $twilio_number, 'body' => $message]
        );
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }

    header('Location: ../recipientform.php?error=false');
} else {
    header('Location: ../recipientform.php?error=true');
}

$conn->close();
?>
