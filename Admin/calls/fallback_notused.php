<?php
require '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the language and call status from the request
$lang = $_GET['lang'];
$dialCallStatus = $_POST['DialCallStatus'];

$response = new VoiceResponse();

// Check if the call was declined, not answered, or failed
if ($dialCallStatus === 'no-answer' || $dialCallStatus === 'busy' || $dialCallStatus === 'failed') {
    // Play a message in the appropriate language
    if ($lang == 'ar-SA') {
        $response->say('عذرًا، الشخص غير متاح حاليًا. يرجى اختيار شخص آخر.', ['language' => $lang]);
    } else {
        $response->say('Sorry, the person is not available right now. Please choose someone else.', ['language' => $lang]);
    }
}

echo $response;