<?php
require '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

// Determine caller type
$callerType = ($_POST['To'] === '+RECIPIENT_NUMBER') ? 'recipient' : 'volunteer';

// Working hours (9:00 AM to 10:00 PM)
$openingHour = 9;
$closingHour = 22;
date_default_timezone_set('America/Chicago');
$currentHour =12+(int) date('h', time());


// Initialize the Twilio Voice Response
$response = new VoiceResponse();

if ($currentHour < $openingHour || $currentHour >= $closingHour) {
    // Outside working hours
    $response->say('Thank you for calling American Islamic Diversity. We are currently closed. Please call us during our working hours from 9 AM to 10 PM.', ['language' => 'en-US']);
    $response->say('شكراً لاتصالكم بـ التنوع الإسلامي الأمريكي. نحن مغلقون حالياً. يرجى الاتصال بنا خلال ساعات العمل من التاسعة صباحاً حتى الخامسة مساءً.', ['language' => 'ar-SA']);
    $response->hangup();
    echo $response;
    exit;
}

// Thank the caller and ask for language preference
$response->say('Thank you for calling American Islamic Diversity.', ['language' => 'en-US']);
$response->say('شكراً لاتصالكم بنا.', ['language' => 'ar-SA']);

$response->gather([
    'numDigits' => 1,
    'action' => "language.php?type=$callerType",
])->say('For English, press 1. For Arabic, press 2.', ['language' => 'en-US']);
// $response->say('للغة العربية، اضغط ٢.', ['language' => 'ar-SA']);



echo $response;
