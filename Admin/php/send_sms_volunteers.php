<?php
session_start();
require('../../php/db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$volunteers = $data['volunteers'] ?? [];
$message = $data['message'] ?? '';
$saveTemplate = $data['saveTemplate'] ?? false;

if (empty($volunteers) || empty($message)) {
    echo json_encode(['message' => 'No volunteers or message provided']);
    exit;
}

// Save the message as a template if the checkbox was checked
if ($saveTemplate) {
    $stmt = $conn->prepare("INSERT INTO sms_templates (template_text, table_name) VALUES ( ?, 'volunteer')");
    $stmt->bind_param('s', $message);
    $stmt->execute();
    $stmt->close();
}

// Example: Using Twilio to send SMS
require '../vendor/autoload.php'; // Composer autoload
use Twilio\Rest\Client;

$account_sid = 'TWILIO_ACCOUNT_SID'; // Replace with your Twilio account SID
$auth_token = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio auth token
$twilio_number = 'TWILIO_VOLUNTEER_PHONE_NUMBER'; // Replace with your Twilio number

$client = new Client($account_sid, $auth_token);

$successCount = 0;
$failureCount = 0;

// limit the foreach loop to 10 volunteers
// $volunteers = array_slice($volunteers, 0, 2);

foreach ($volunteers as $volunteerId) {
    $result = $conn->query("SELECT phone FROM volunteer WHERE volunteer_id = " . intval($volunteerId));
    $volunteer = $result->fetch_assoc();
    $errorMessage = '';
    try {
        $client->messages->create(
            $volunteer['phone'],
            ['from' => $twilio_number, 'body' => $message]
        );
        $successCount++;
        $status = 'success';
    } catch (Exception $e) {
        $status = 'failed';
        $errorMessage = $e->getMessage();
        $failureCount++;
    }
    $sql  = "insert into sms_logs (user_phone, sent_message, sent_by, status, error_message, user) values ('$volunteer[phone]', '$message', $_SESSION[admin_id], '$status', '$errorMessage', 'volunteer')";
    $conn->query($sql);
}

// Log the message
$volunteersCount = count($volunteers);
$sql  = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'SMS_SENT', 'volunteer', 0, 'Sent SMS to $volunteersCount volunteers. Using this message: $message')";
$conn->query($sql);

echo json_encode(['message' => "SMS sent to $successCount numbers; failed for $failureCount."]);
?>
