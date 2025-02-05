<?php
include 'db.php';
include '../Admin/vendor/autoload.php';
use Twilio\Rest\Client;

error_reporting(E_ALL);
ini_set('display_errors', 1);

function normalize_phone_number($phone) {
    return preg_replace(['/^\+1|^1/', '/\)/', '/\(/', '/\s+/', '/\+/', '/-/', '/\s1/'], '', $phone);
}

$full_name = $_POST['full_name'];
$phone = normalize_phone_number($_POST['phone']);
$email = $_POST['email'];
$zip_code = $_POST['zip_code'];
$language = $_POST['language'];
$preference = $_POST['preference'];
$car_size = $_POST['car_size'] ?? 'none';
$notf_preference = $_POST['event_names'] ?? [];
$notf_preference = implode(", ", $notf_preference);    

$sql = "INSERT INTO volunteer (full_name, phone, email, preference, car_size, zip_code, language, notf_preference)
VALUES ('$full_name', '$phone', '$email', '$preference' , '$car_size', $zip_code, '$language', '$notf_preference')";

if ($conn->query($sql) === TRUE) {

        $account_sid = 'TWILIO_ACCOUNT_SID'; // Replace with your Twilio account SID
        $auth_token = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio auth token
        $twilio_number = 'TWILIO_VOLUNTEER_PHONE_NUMBER'; // Replace with your Twilio number

        $client = new Client($account_sid, $auth_token);
        $message = "";
        if ($language == 'Arabic') {
            $message = "عزيزي/عزيزتي ".$full_name."،\n\nشكرًا جزيلاً لتسجيلك للتطوع مع التنوع الإسلامي الأمريكي (AID)!\n\nأنا ربا قعوار، الرئيس التنفيذي للمنظمة. أرحب بكم ترحيبًا حارًا، وأسأل الله أن يبارك في كل ما تقولون وتفعلون.\n\nيرجى حفظ هذا الرقم: +1 (469) 960-4650، حيث سيتم استخدامه لجميع الاتصالات المتعلقة بأنشطة التطوع الخاصة بكم.\n\n**ملاحظات مهمة:**\n- يعمل بنك الطعام لدينا كل يوم سبت الأول من الشهر.\n- إذا كانت لديكم أي أسئلة أو تحتاجون إلى مساعدة، يرجى الاتصال بنا على هذا الرقم.\n\nشكرًا لتفانيكم وتعاونكم. نحن ممتنون لوجودكم كجزء من فريقنا!\n\nجزاكم الله خيرًا على جهودكم.";
        } else if ($language == 'Farsi') {
            $message = "عزیز ".$full_name."،\n\nاز ثبت نام شما برای داوطلبی در تنوع اسلامی آمریکایی (AID) بسیار سپاسگزاریم!\n\nمن ربا قعوار، مدیرعامل سازمان هستم. از شما گرمأگویی می‌کنم و از خداوند می‌خواهم که در هر آنچه می‌گویید و انجام می‌دهید برکت دهد.\n\nلطفاً این شماره را ذخیره کنید: +1 (469) 960-4650، زیرا برای تمامی ارتباطات مربوط به فعالیت‌های داوطلبانه شما استفاده خواهد شد.\n\n**نکات مهم:**\n- بانک غذای ما هر شنبه اول ماه فعالیت می‌کند.\n- اگر سوالی دارید یا به کمک نیاز دارید، لطفاً با این شماره تماس بگیرید.\n\nاز تلاش‌ها و همکاری شما متشکریم. خوشحالیم که شما را به عنوان بخشی از تیم خود داریم!\n\nخداوند به شما پاداش دهد.";
        } else if ($language == 'Spanish') {
            $message = "Estimado/a ".$full_name.",\n\n¡Muchas gracias por registrarte como voluntario/a con American Islamic Diversity (AID)!\n\nMi nombre es Ruba Qewar, la CEO de la organización. Les doy una cálida bienvenida y pido a Allah que los bendiga en todo lo que digan y hagan.\n\nPor favor, guarde este número: +1 (469) 960-4650, ya que se utilizará para todas las comunicaciones relacionadas con sus actividades de voluntariado.\n\n**Notas importantes:**\n- Nuestro banco de alimentos opera cada primer sábado del mes.\n- Si tiene alguna pregunta o necesita ayuda, por favor contáctenos en este número.\n\nGracias por su dedicación y cooperación. ¡Estamos agradecidos de tenerlos como parte de nuestro equipo!\n\nQue Allah los recompense por sus esfuerzos.";
        } else if ($language == 'Urdu') {
            $message = "محترم ".$full_name."\n\nامریکن اسلامی ڈائیورسٹی (AID) کے ساتھ رضاکارانہ طور پر رجسٹر کرنے کا بہت بہت شکریہ!\n\nمیں ربا قعوار، تنظیم کی سی ای او ہوں۔ میں آپ کا گرمجوشی سے خیرمقدم کرتی ہوں، اور میں اللہ سے دعا کرتی ہوں کہ وہ آپ کے ہر قول اور فعل میں برکت دے۔\n\nبراہ کرم اس نمبر کو محفوظ کریں: +1 (469) 960-4650، کیونکہ یہ آپ کی رضاکارانہ سرگرمیوں سے متعلق تمام مواصلات کے لیے استعمال ہوگا۔\n\n**اہم نوٹس:**\n- ہمارا فوڈ پینٹری ہر مہینے کے پہلے ہفتے کو کام کرتا ہے۔\n- اگر آپ کے کوئی سوالات ہیں یا مدد کی ضرورت ہے، تو براہ کرم اس نمبر پر ہم سے رابطہ کریں۔\n\nآپ کی لگن اور تعاون کا شکریہ۔ ہم آپ کو اپنی ٹیم کا حصہ بننے پر شکر گزار ہیں!\n\nاللہ آپ کی کوششوں کو قبول فرمائے۔";
        } else if ($language == 'Myanmar') {
            $message = "ချစ်စရာ ".$full_name."\n\nAmerican Islamic Diversity (AID) တွင် လုပ်အားပေးအဖြစ် မှတ်ပုံတင်သည့်အတွက် ကျေးဇူးအများကြီးတင်ပါသည်!\n\nကျွန်မနာမည်က Ruba Qewar ဖြစ်ပြီး အဖွဲ့အစည်း၏ CEO ဖြစ်ပါသည်။ ကျွန်မသည် သင့်ကို ကြိုဆိုပါသည်၊ ပြီးလျှင် သင်၏ စကားနှင့် လုပ်ဆောင်ချက်တိုင်းတွင် အလ္လာဟ်အရှင်မြတ်၏ ကောင်းချီးမင်္ဂလာများ ရရှိပါစေဟု ဆုတောင်းပါသည်။\n\nကျေးဇူးပြု၍ ဤနံပါတ်ကို သိမ်းဆည်းထားပါ: +1 (469) 960-4650၊ အဘယ်ကြောင့်ဆိုသော် သင်၏ လုပ်အားပေးလုပ်ငန်းများနှင့်ပတ်သက်သည့် ဆက်သွယ်ရေးအတွက် ဤနံပါတ်ကို အသုံးပြုမည်ဖြစ်သည်။\n\n**အရေးကြီးသော မှတ်ချက်များ:**\n- ကျွန်ုပ်တို့၏ အစားအစာဘဏ်သည် လတိုင်း၏ ပထမဆုံး စနေနေ့တွင် လုပ်ဆောင်ပါသည်။\n- သင့်တွင် မေးခွန်းများရှိပါက သို့မဟုတ် အကူအညီလိုအပ်ပါက ကျေးဇူးပြု၍ ဤနံပါတ်သို့ ဆက်သွယ်ပါ။\n\nသင်၏ အားထုတ်မှုနှင့် ပူးပေါင်းဆောင်ရွက်မှုအတွက် ကျေးဇူးတင်ပါသည်။ သင့်�ကို ကျွန်ုပ်တို့၏ အဖွဲ့၏ အစိတ်အပိုင်းတစ်ခုအဖြစ် ရရှိသည်မှာ ကျေးဇူးတင်ပါသည်!\n\nအလ္လာဟ်အရှင်မြတ်သည် သင်၏ ကြိုးပမ်းမှုများကို အကျိုးပေးပါစေ။";
        } else if ($language == 'Pashto') {
            $message = "ګرانه ".$full_name."\n\nد امریکایی اسلامي تنوع (AID) سره د رضاکارۍ لپاره د ثبت نام ډیره مننه!\n\nزما نوم ربا قعوار دی، د سازمان CEO یم. زه تاسو ته ګرم استقبال کوم او له الله څخه غواړم چې ستاسو په هر خبره او عمل کې برکت ورکړي.\n\nمهرباني وکړئ دا شمیره خوندي کړئ: +1 (469) 960-4650، ځکه چې د ستاسو د رضاکارۍ فعالیتونو په اړه ټولې اړیکې به د دې شمیرې په کارولو سره ترسره شي.\n\n**مهم نوټونه:**\n- زموږ د خواړه پانټري هر میاشتې د لومړي شنبې په ورځ کار کوي.\n- که تاسو کوم پوښتنې لرئ یا مرستې ته اړتیا لرئ، مهرباني وکړئ د دې شمیرې په مرسته موږ سره اړیکه ونیسئ.\n\nستاسو د هڅو او همکارۍ لپاره مننه. موږ خوښ یو چې تاسو زموږ د ټیم برخه یاست!\n\nالله ستاسو هڅې ومني.";
        } else {
            $message = "Dear ".$full_name.",\n\nThank you so much for signing up to volunteer with American Islamic Diversity (AID)!\n\nMy name is Ruba Qewar, the CEO of the organization. I warmly welcome you, and I ask Allah to bless you in everything you say and do.\n\nPlease save this number: +1 (469) 960-4650, as it will be used for all communication regarding your volunteer activities.\n\n**Important Notes:**\n- Our food pantry operates every first Saturday of the month.\n- If you have any questions or need assistance, please contact us at this number.\n\nThank you for your dedication and cooperation. We are grateful to have you as part of our team!\n\nMay Allah reward you for your efforts.";
        }
        
        try {
            $client->messages->create(
                $phone,
                ['from' => $twilio_number, 'body' => $message]
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

    header('Location: ../volunteerform.php?error=false');
} else {
    header('Location: ../volunteerform.php?error=true');
}

$conn->close();
?>
