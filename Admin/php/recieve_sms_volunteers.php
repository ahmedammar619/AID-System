<?php
require '../../php/db.php'; // Include your database connection
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure proper response code
http_response_code(200);

// Log incoming requests for debugging
file_put_contents('incoming_sms_log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

// Get the raw POST data
$input = file_get_contents('php://input');

// Decode JSON input (if your SMS provider sends JSON)
$data = json_decode($input, true);

// If JSON decoding fails, assume form data (e.g., Twilio sends form data)
if (json_last_error() !== JSON_ERROR_NONE) {
    $data = $_POST;
}

// Validate required fields
if (empty($data['From']) || empty($data['Body'])) {
    echo json_encode(['message' => 'Invalid request: Missing required fields']);
    exit;
}

// Extract data
$from = $data['From']; // Sender's phone number
$body = trim($data['Body']); // Message content


// Normalize phone number: remove non-digits, then leading '1' if present
$from = preg_replace('/[^0-9]/', '', $from);
if (strlen($from) === 11 && $from[0] === '1') {
    $from = substr($from, 1); // Remove leading '1' for 11-digit numbers
}

// Log the incoming SMS
file_put_contents('incoming_sms_log.txt', "From: $from, Body: $body" . PHP_EOL, FILE_APPEND);

// Process the message
$responseMessage = processSms($from, $body);

// Respond with JSON (Twilio expects a valid HTTP response)
// Send a TwiML response
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<Response>';
echo '<Message>' . htmlspecialchars($responseMessage) . '</Message>';
echo '</Response>';
function processSms($from, $body) {
    global $conn;

    // Normalize the phone number (remove '+' and any non-numeric characters) and remove the number 1 if it was the first character
    $from = preg_replace('/[^0-9]/', '', $from);
    $from = preg_replace('/\+|1/', '', $from);


    // Fetch the volunteer from the database
    $stmt = $conn->prepare("SELECT volunteer_id, language FROM volunteer WHERE phone = ?");
    $stmt->bind_param('s', $from);
    $stmt->execute();
    $result = $stmt->get_result();
    $volunteer = $result->fetch_assoc();
    $stmt->close();

    if (!$volunteer) {
        // return "Error: volunteer not found.";
    }

    // Parse the message content
    $body = strtolower($body); // Convert to lowercase for easier comparison
    $responseMessage = "Thank you for your response!";
    $language = $volunteer['language'];
    switch ($body) {
        case '1':
            updatevolunteerStatus($volunteer['volunteer_id'], 'Delivery');
            $responseMessage = getResponseMessage('Delivery', $language);
            break;
    
        case '2':
            updatevolunteerStatus($volunteer['volunteer_id'], 'Packing');
            $responseMessage = getResponseMessage('Packing', $language);
            break;
    
        case '3':
            updatevolunteerStatus($volunteer['volunteer_id'], 'Both');
            $responseMessage = getResponseMessage('Both', $language);
            break;
    
        case '4':
            updatevolunteerStatus($volunteer['volunteer_id'], 'Next month');
            $responseMessage = getResponseMessage('Next month', $language);
            break;
    
        case '5':
            updatevolunteerStatus($volunteer['volunteer_id'], 'Delete from list');
            $responseMessage = getResponseMessage('Delete from list', $language);
            break;
    
        default:
            $sql = "select sent_at from sms_logs where user_phone = '$from' and sent_at > NOW() - interval 24 hour order by sent_at desc limit 1";
            $result = $conn->query($sql);
            if($result->fetch_assoc()['sent_at']){
                $responseMessage = getResponseMessage('Invalid', $language);
            }
            break;
    }

    // Log the action
    logSmsAction( $from, $body);
    return $responseMessage;
}

function updatevolunteerStatus($volunteerId, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE volunteer SET replied = ?, replied_date = NOW() WHERE volunteer_id = ?");
    $stmt->bind_param('si', $status, $volunteerId);
    $stmt->execute();
    $stmt->close();
}

function logSmsAction($from, $body) {
    global $conn;
    $sql = "update sms_logs set recieved = '$body', recieved_at = NOW() where user_phone = '$from' order by sms_id desc limit 1";
    $conn->query($sql);
}

function getResponseMessage($status, $language) {
    switch ($language) {
        case 'English':
            switch ($status) {
                case 'Delivery':
                    return "Thank you for your response! Your status has been updated to 'Delivery'. We appreciate your willingness to help with deliveries!";
                case 'Packing':
                    return "Thank you for your response! Your status has been updated to 'Packing'. We appreciate your willingness to help with packing!";
                case 'Both':
                    return "Thank you for your response! Your status has been updated to 'Both'. We appreciate your willingness to help with both deliveries and packing!";
                case 'Next month':
                    return "Thank you for your response! Your status has been updated to 'Next month'. We look forward to your help next month!";
                case 'Delete from list':
                    return "Thank you for your response! Your status has been updated to 'Delete from list'. We appreciate your past contributions and hope to see you again in the future!";
                default:
                    return "Sorry, we didn't understand your response. Please reply with 1, 2, 3, 4 or 5. If you have any questions, please contact us at +1 (469) 960-4650.";
            }
            

        case 'Arabic':
            switch ($status) {
                case 'Delivery':
                    return "شكرًا على ردك! تم تحديث حالتك إلى 'توصيل'. نقدر استعدادك للمساعدة في التوصيلات!";
                case 'Packing':
                    return "شكرًا على ردك! تم تحديث حالتك إلى 'تعبئة'. نقدر استعدادك للمساعدة في التعبئة!";
                case 'Both':
                    return "شكرًا على ردك! تم تحديث حالتك إلى 'كليهما'. نقدر استعدادك للمساعدة في التوصيلات والتعبئة!";
                case 'Next month':
                    return "شكرًا على ردك! تم تحديث حالتك إلى 'الشهر القادم'. نتطلع إلى مساعدتك الشهر القادم!";
                case 'Delete from list':
                    return "شكرًا على ردك! تم تحديث حالتك إلى 'حذف من القائمة'. نقدر مساهماتك السابقة ونأمل أن نراك مرة أخرى في المستقبل!";
                default:
                    return "عذرًا، لم نفهم ردك. يرجى الرد بـ 1 أو 2 أو 3 أو 4 أو 5. إذا كان لديك أي أسئلة، يرجى الاتصال بنا على +1 (469) 960-4650.";
            }
            

        case 'Farsi':
            switch ($status) {
                case 'Delivery':
                    return "از پاسخ شما متشکریم! وضعیت شما به 'تحویل' به‌روزرسانی شد. از تمایل شما برای کمک در تحویل‌ها قدردانی می‌کنیم!";
                case 'Packing':
                    return "از پاسخ شما متشکریم! وضعیت شما به 'بسته‌بندی' به‌روزرسانی شد. از تمایل شما برای کمک در بسته‌بندی قدردانی می‌کنیم!";
                case 'Both':
                    return "از پاسخ شما متشکریم! وضعیت شما به 'هر دو' به‌روزرسانی شد. از تمایل شما برای کمک در تحویل‌ها و بسته‌بندی قدردانی می‌کنیم!";
                case 'Next month':
                    return "از پاسخ شما متشکریم! وضعیت شما به 'ماه آینده' به‌روزرسانی شد. منتظر کمک شما در ماه آینده هستیم!";
                case 'Delete from list':
                    return "از پاسخ شما متشکریم! وضعیت شما به 'حذف از لیست' به‌روزرسانی شد. از مشارکت‌های گذشته شما قدردانی می‌کنیم و امیدواریم در آینده دوباره شما را ببینیم!";
                default:
                    return "اوه! به نظر می‌رسد گزینه نامعتبری وارد کرده‌اید. لطفاً دوباره امتحان کنید. از صبر شما متشکریم!";
            }
            

        case 'Spanish':
            switch ($status) {
                case 'Delivery':
                    return "¡Gracias por tu respuesta! Tu estado ha sido actualizado a 'Entrega'. ¡Agradecemos tu disposición para ayudar con las entregas!";
                case 'Packing':
                    return "¡Gracias por tu respuesta! Tu estado ha sido actualizado a 'Empaque'. ¡Agradecemos tu disposición para ayudar con el empaque!";
                case 'Both':
                    return "¡Gracias por tu respuesta! Tu estado ha sido actualizado a 'Ambos'. ¡Agradecemos tu disposición para ayudar con entregas y empaque!";
                case 'Next month':
                    return "¡Gracias por tu respuesta! Tu estado ha sido actualizado a 'Próximo mes'. ¡Esperamos tu ayuda el próximo mes!";
                case 'Delete from list':
                    return "¡Gracias por tu respuesta! Tu estado ha sido actualizado a 'Eliminar de la lista'. ¡Agradecemos tus contribuciones pasadas y esperamos verte de nuevo en el futuro!";
                default:
                    return "¡Ups! Parece que ingresaste una opción no válida. Por favor, inténtalo de nuevo. ¡Gracias por tu paciencia!";
            }
            

        case 'Urdu':
            switch ($status) {
                case 'Delivery':
                    return "آپ کے جواب کا شکریہ! آپ کی حیثیت 'ڈیلیوری' میں اپ ڈیٹ کردی گئی ہے۔ ڈیلیوری میں مدد کرنے کے لیے آپ کی آمادگی کی ہم تعریف کرتے ہیں!";
                case 'Packing':
                    return "آپ کے جواب کا شکریہ! آپ کی حیثیت 'پیکنگ' میں اپ ڈیٹ کردی گئی ہے۔ پیکنگ میں مدد کرنے کے لیے آپ کی آمادگی کی ہم تعریف کرتے ہیں!";
                case 'Both':
                    return "آپ کے جواب کا شکریہ! آپ کی حیثیت 'دونوں' میں اپ ڈیٹ کردی گئی ہے۔ ڈیلیوری اور پیکنگ میں مدد کرنے کے لیے آپ کی آمادگی کی ہم تعریف کرتے ہیں!";
                case 'Next month':
                    return "آپ کے جواب کا شکریہ! آپ کی حیثیت 'اگلے مہینے' میں اپ ڈیٹ کردی گئی ہے۔ ہم اگلے مہینے آپ کی مدد کا منتظر ہیں!";
                case 'Delete from list':
                    return "آپ کے جواب کا شکریہ! آپ کی حیثیت 'فہرست سے حذف' میں اپ ڈیٹ کردی گئی ہے۔ ہم آپ کی گزشتہ شراکت کی تعریف کرتے ہیں اور امید کرتے ہیں کہ مستقبل میں آپ کو دوبارہ دیکھیں گے!";
                default:
                    return "اوہ! لگتا ہے آپ نے غلط آپشن منتخب کیا ہے۔ براہ کرم دوبارہ کوشش کریں۔ آپ کے صبر کا شکریہ!";
            }
            

        case 'Myanmar':
            switch ($status) {
                case 'Delivery':
                    return "သင့်အကြောင်းပြန်ချက်အတွက် ကျေးဇူးတင်ပါသည်! သင့်အခြေအနေကို 'ပို့ဆောင်ခြင်း' အဖြစ် အပ်ဒိတ်လုပ်ပြီးပါပြီ။ ပို့ဆောင်မှုများတွင် ကူညီပေးရန် သင့်ဆန္ဒကို ကျေးဇူးတင်ပါသည်!";
                case 'Packing':
                    return "သင့်အကြောင်းပြန်ချက်အတွက် ကျေးဇူးတင်ပါသည်! သင့်အခြေအနေကို 'ထုပ်ပိုးခြင်း' အဖြစ် အပ်ဒိတ်လုပ်ပြီးပါပြီ။ ထုပ်ပိုးမှုတွင် ကူညီပေးရန် သင့်ဆန္ဒကို ကျေးဇူးတင်ပါသည်!";
                case 'Both':
                    return "သင့်အကြောင်းပြန်ချက်အတွက် ကျေးဇူးတင်ပါသည်! သင့်အခြေအနေကို 'နှစ်ခုလုံး' အဖြစ် အပ်ဒိတ်လုပ်ပြီးပါပြီ။ ပို့ဆောင်မှုနှင့် ထုပ်ပိုးမှုတွင် ကူညီပေးရန် သင့်ဆန္ဒကို ကျေးဇူးတင်ပါသည်!";
                case 'Next month':
                    return "သင့်အကြောင်းပြန်ချက်အတွက် ကျေးဇူးတင်ပါသည်! သင့်အခြေအနေကို 'လာမည့်လ' အဖြစ် အပ်ဒိတ်လုပ်ပြီးပါပြီ။ လာမည့်လတွင် သင့်ကူညီမှုကို မျှော်လင့်ပါသည်!";
                case 'Delete from list':
                    return "သင့်အကြောင်းပြန်ချက်အတွက် ကျေးဇူးတင်ပါသည်! သင့်အခြေအနေကို 'စာရင်းမှ ဖျက်ပစ်ရန်' အဖြစ် အပ်ဒိတ်လုပ်ပြီးပါပြီ။ သင့်အရင်ကကူညီမှုများကို ကျေးဇူးတင်ပါသည် နှင့် အနာဂတ်တွင် သင့်ကို ထပ်မံတွေ့ရှိရမည်ကို မျှော်လင့်ပါသည်!";
                default:
                    return "အိုး! သင် မှားယွင်းသောရွေးချယ်မှုကို ရွေးချယ်ထားသည်ဟု ထင်ရသည်။ ကျေးဇူးပြု၍ ထပ်ကြိုးစားပါ။ သင့်စိတ်ရှည်မှုအတွက် ကျေးဇူးတင်ပါသည်!";
            }
            

        case 'Pashto':
            switch ($status) {
                case 'Delivery':
                    return "ستاسو د ځواب لپاره مننه! ستاسو حالت 'تحویل' ته تازه شوی. موږ ستاسو د تحویلو سره د مرستې لپاره د ستاسو لیوالتیا څخه مننه کوو!";
                case 'Packing':
                    return "ستاسو د ځواب لپاره مننه! ستاسو حالت 'بسته‌بندي' ته تازه شوی. موږ ستاسو د بسته‌بندي سره د مرستې لپاره د ستاسو لیوالتیا څخه مننه کوو!";
                case 'Both':
                    return "ستاسو د ځواب لپاره مننه! ستاسو حالت 'دواړه' ته تازه شوی. موږ ستاسو د تحویلو او بسته‌بندي سره د مرستې لپاره د ستاسو لیوالتیا څخه مننه کوو!";
                case 'Next month':
                    return "ستاسو د ځواب لپاره مننه! ستاسو حالت 'بل میاشت' ته تازه شوی. موږ بل میاشت کې ستاسو د مرستې هیله لرو!";
                case 'Delete from list':
                    return "ستاسو د ځواب لپاره مننه! ستاسو حالت 'د لیست څخه حذف' ته تازه شوی. موږ ستاسو د تیرو مرستو څخه مننه کوو او هیله لرو چې په راتلونکي کې بیا ستاسو سره وګورو!";
                default:
                    return "افسوس! ښکاري چې تاسو ناسمه اختیار غوره کړې. مهرباني وکړئ بیا هڅه وکړئ. ستاسو د صبر لپاره مننه!";
            }
            

        case 'Other':
        default:
            switch ($status) {
                case 'Delivery':
                    return "Thank you for your response! Your status has been updated to 'Delivery'. We appreciate your willingness to help with deliveries!";
                case 'Packing':
                    return "Thank you for your response! Your status has been updated to 'Packing'. We appreciate your willingness to help with packing!";
                case 'Both':
                    return "Thank you for your response! Your status has been updated to 'Both'. We appreciate your willingness to help with both deliveries and packing!";
                case 'Next month':
                    return "Thank you for your response! Your status has been updated to 'Next month'. We look forward to your help next month!";
                case 'Delete from list':
                    return "Thank you for your response! Your status has been updated to 'Delete from list'. We appreciate your past contributions and hope to see you again in the future!";
                default:
                    return "Oops! It looks like you entered an invalid option. Please try again. Thank you for your patience!";
            }
            
    }
}

?>