<?php
require '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$type = $_GET['type'];
$lang = $_GET['lang'];
$digit = $_POST['Digits'];
$callerPhone = $_POST['From']; // The caller's phone number
$response = new VoiceResponse();

$contacts = [
    'recipient' => [
        1 => '+RUBA_PHONE', // ruba
        2 => '+YOUSEF_PHONE', // yousef
        3 => '+ORDY_PHONE', // ordy
    ],
    'volunteer' => [
        1 => '+RUBA_PHONE', // ruba
        2 => '+OMAR_PHONE', // omar
        3 => '+ABED_PHONE', //abed
    ]
];

require '../../php/db.php';
// Fetch the caller's name from the database
$sql = "SELECT full_name FROM $type WHERE phone = $callerPhone";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$callerName = null;
$callerRole = $type;

if ($row = $result->fetch_assoc()) {
    $callerName = $row['full_name'];
    $callerRole = $type;
} else {
    $callerRole = 'unknown';
}
$stmt->close();
$conn->close();

// Prepare the whisper message
if ($callerRole === 'volunteer' || $callerRole === 'recipient') {
    $whisperMessage = "You are receiving a call from a $callerRole named $callerName.";
} else {
    $whisperMessage = "You are receiving a call from an unregistered number.";
}

// Dial the target with the whisper message
if (isset($contacts[$type][$digit])) {
    $target = $contacts[$type][$digit];
    $dial = $response->dial($target);
    $dial->number($target, [
        'url' => "whisper.php?lang=$lang&message=" . urlencode($whisperMessage)
    ]);
} else {
    if($lang == 'ar-SA') $response->say('اختيار غير صالح. مع السلامة.', ['language' => $lang]);
    else $response->say('Invalid choice. Goodbye.', ['language' => $lang]);
    $response->hangup();
}

echo $response;

