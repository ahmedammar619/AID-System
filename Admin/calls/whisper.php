<?php
require '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

$lang = $_GET['lang'];
$message = urldecode($_GET['message']);

$response = new VoiceResponse();

// Play the warning message and the whisper message
$response->say("You are receiving a call from American Islamic Diversity.", ['language' => $lang]);
$response->pause(['length' => 1]);
$response->say($message, ['language' => $lang]);


echo $response;
