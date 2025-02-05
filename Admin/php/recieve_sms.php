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


    // Fetch the recipient from the database
    $stmt = $conn->prepare("SELECT recipient_id, language FROM recipient WHERE phone = ?");
    $stmt->bind_param('s', $from);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipient = $result->fetch_assoc();
    $stmt->close();

    if (!$recipient) {
        // return "Error: recipient not found.";
    }

    // Parse the message content
    $body = strtolower($body); // Convert to lowercase for easier comparison
    $responseMessage = "Thank you for your response!";
    $language = $recipient['language'];
    switch ($body) {
        case '1':
            // Update recipient status to "Owns a car"
            updateRecipientStatus($recipient['recipient_id'], 'Owns a car');
            $responseMessage = getResponseMessage('Car', $language);
            break;
            
        case '2':
        case 'yes':
            // Update recipient status to "Yes"
            updateRecipientStatus($recipient['recipient_id'], 'Yes');
            $responseMessage = getResponseMessage('Yes', $language);
            break;
    
        case '3':
        case 'no':
            // Update recipient status to "No"
            updateRecipientStatus($recipient['recipient_id'], 'Next month');
            $responseMessage = getResponseMessage('No', $language);
            break;
        case '4':
        case 'remove':
            // Update recipient status to "Remove from list"
            updateRecipientStatus($recipient['recipient_id'], 'Delete from list');
            $responseMessage = getResponseMessage('Remove', $language);
            break;
    
        default:
            // Handle unknown responses
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

function updaterecipientStatus($recipientId, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE recipient SET replied = ?, replied_date = NOW() WHERE recipient_id = ?");
    $stmt->bind_param('si', $status, $recipientId);
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
        case 'Arabic':
            switch ($status) {
                case 'Yes':
                    return "شكرًا لتأكيدك! سنقوم بتوصيل صندوقك يوم السبت.";
                case 'No':
                    return "سنقوم بالاتصال بك الشهر القادم. شكرًا لك!";
                case 'Remove':
                    return "تمت إزالتك من قائمتنا. شكرًا لك!";
                case 'Car':
                    return "سنكون في انتظارك لاستلام صندوقك!";
                default:
                    return "عذرًا، لم نفهم ردك. يرجى الرد بـ 1 أو 2 أو 3. إذا كان لديك أي أسئلة، يرجى الاتصال بنا على +1 (469) 960-4655.";
            }

        case 'Farsi':
            switch ($status) {
                case 'Yes':
                    return "از تأیید شما متشکریم! جعبه شما را روز شنبه تحویل خواهیم داد.";
                case 'No':
                    return "ماه آینده با شما تماس خواهیم گرفت. متشکرم!";
                case 'Remove':
                    return "شما از لیست ما حذف شده‌اید. متشکرم!";
                case 'Car':
                    return "منتظر شما خواهیم بود تا جعبه خود را تحویل بگیرید!";
                default:
                    return "متأسفیم، پاسخ شما را متوجه نشدیم. لطفاً با 1، 2 یا 3 پاسخ دهید. اگر سؤالی دارید، لطفاً با ما تماس بگیرید: +1 (469) 960-4655.";
            }

        case 'Spanish':
            switch ($status) {
                case 'Yes':
                    return "¡Gracias por confirmar! Entregaremos su caja el sábado.";
                case 'No':
                    return "Nos pondremos en contacto contigo el próximo mes. ¡Gracias!";
                case 'Remove':
                    return "Has sido eliminado de nuestra lista. ¡Gracias!";
                case 'Car':
                    return "¡Estaremos esperando para que recoja su caja!";
                default:
                    return "Lo sentimos, no entendimos tu respuesta. Por favor, responde con 1, 2 o 3. Si tienes alguna pregunta, contáctanos al +1 (469) 960-4655.";
            }

        case 'Urdu':
            switch ($status) {
                case 'Yes':
                    return "تصدیق کرنے کا شکریہ! ہم آپ کا باکس ہفتہ کے روز ڈیلیور کریں گے۔";
                case 'No':
                    return "ہم آپ سے اگلے مہینے رابطہ کریں گے۔ شکریہ!";
                case 'Remove':
                    return "آپ کو ہماری فہرست سے ہٹا دیا گیا ہے۔ شکریہ!";
                case 'Car':
                    return "ہم آپ کا باکس لینے کے لیے انتظار کریں گے!";
                default:
                    return "معذرت، ہم آپ کا جواب نہیں سمجھ سکے۔ براہ کرم 1، 2 یا 3 کے ساتھ جواب دیں۔ اگر آپ کے کوئی سوالات ہیں، تو براہ کرم ہم سے +1 (469) 960-4655 پر رابطہ کریں۔";
            }

        case 'Myanmar':
            switch ($status) {
                case 'Yes':
                    return "အတည်ပြုပေးတဲ့အတွက် ကျေးဇူးတင်ပါတယ်! သင့်ရဲ့ပစ္စည်းကို စနေနေ့မှာ ပို့ဆောင်ပေးပါမယ်။";
                case 'No':
                    return "လာမယ့်လမှာ သင့်ကို ပြန်လည်ဆက်သွယ်ပါမယ်။ ကျေးဇူးတင်ပါတယ်!";
                case 'Remove':
                    return "သင့်ကို ကျွန်ုပ်တို့စာရင်းမှ ဖယ်ရှားလိုက်ပါပြီ။ ကျေးဇူးတင်ပါတယ်!";
                case 'Car':
                    return "သင့်ရဲ့ပစ္စည်းကို လာယူဖို့ ကျွန်ုပ်တို့ စောင့်မျှော်နေပါမယ်!";
                default:
                    return "ဝမ်းနည်းပါတယ်၊ သင့်ရဲ့အကြောင်းပြန်ချက်ကို နားမလည်ပါ။ 1၊ 2 သို့မဟုတ် 3 ဖြင့် ပြန်လည်အကြောင်းပြန်ပါ။ မေးခွန်းများရှိပါက +1 (469) 960-4655 သို့ ဆက်သွယ်ပါ။";
            }

        case 'Pashto':
            switch ($status) {
                case 'Yes':
                    return "د تایید لپاره مننه! موږ به ستاسو بکس په شنبه ورځ وړاندې کړو.";
                case 'No':
                    return "موږ به په راتلونکي میاشت کې له تاسو سره اړیکه ونیسو. مننه!";
                case 'Remove':
                    return "تاسو زموږ له لیست څخه لرې شوی یاست. مننه!";
                case 'Car':
                    return "موږ به ستاسو د بکس د رااخیستلو لپاره انتظار وکړو!";
                default:
                    return "بخښنه غواړو، موږ ستاسو ځواب نه پوهیدو. مهرباني وکړئ د 1، 2 یا 3 سره ځواب ورکړئ. که کوم پوښتنې لرئ، مهرباني وکړئ د +1 (469) 960-4655 سره اړیکه ونیسئ.";
            }

        default:
            // Default to English
            switch ($status) {
                case 'Yes':
                    return "Thank you for confirming! We will deliver your Box on Saturday.";
                case 'No':
                    return "We will contact you next month. Thank you!";
                case 'Remove':
                    return "You have been removed from our list. Thank you!";
                case 'Car':
                    return "We will be waiting for you to pick up your Box!";
                default:
                    return "Sorry, we didn't understand your response. Please reply with 1, 2, or 3. If you have any questions, please contact us at +1 (469) 960-4655.";
            }
    }
}

?>