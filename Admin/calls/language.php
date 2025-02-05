<?php
require '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

$type = $_GET['type'];
$language = ($_POST['Digits'] == '1') ? 'en-US' : 'ar-SA';

$response = new VoiceResponse();

// Different menus for recipients and volunteers
if ($type === 'recipient') {
    if($language == 'ar-SA'){
        $response->gather([
          'numDigits' => 1,
          'action' => "route_call.php?type=$type&lang=$language"
      ])->say('للتحدث مع ربا، اضغط ١. للتحدث مع يوسف، اضغط ٢. للتحدث مع محمد، اضغط ٣.', ['language' => $language]);
    }else{
        $response->gather([
            'numDigits' => 1,
            'action' => "route_call.php?type=$type&lang=$language"
        ])->say('To talk to Ruba, press 1. To talk to Yousef, press 2. To talk to Mohamed, press 3.', ['language' => $language]);
    }
      
} else {
    if($language == 'ar-SA'){
        $response->gather([
          'numDigits' => 1,
          'action' => "route_call.php?type=$type&lang=$language"
      ])->say('للتحدث مع ربا، اضغط ١. للتحدث مع عمر، اضغط ٢. للتحدث مع عبد عفيفي، اضغط ٣.', ['language' => $language]);
    }else{
        $response->gather([
            'numDigits' => 1,
            'action' => "route_call.php?type=$type&lang=$language"
        ])->say('To talk to Ruba, press 1. To talk to Omar, press 2. To talk to Abed Afifi, press 3.', ['language' => $language]);
    }
}
 

echo $response;
