<?php
session_start();
require('../../php/db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$recipients = $data['recipients'] ?? [];
$message = $data['message'] ?? '';
$saveTemplate = $data['saveTemplate'] ?? false;

if (empty($recipients) || empty($message)) {
    echo json_encode(['message' => 'No recipients or message provided']);
    exit;
}

// Save the message as a template if the checkbox was checked
if ($saveTemplate) {
    $stmt = $conn->prepare("INSERT INTO sms_templates (template_text, table_name) VALUES ( ?, 'recipient')");
    $stmt->bind_param('s', $message);
    $stmt->execute();
    $stmt->close();
}

// Example: Using Twilio to send SMS
require '../vendor/autoload.php'; // Composer autoload
use Twilio\Rest\Client;

$account_sid = 'TWILIO_ACCOUNT_SID'; // Replace with your Twilio account SID
$auth_token = 'TWILIO_AUTH_TOKEN'; // Replace with your Twilio auth token
$twilio_number = 'TWILIO_RECIPIENT_PHONE_NUMBER'; // Replace with your Twilio number

$client = new Client($account_sid, $auth_token);

$successCount = 0;
$failureCount = 0;

// limit the foreach loop to 10 recipients
// $recipients = array_slice($recipients, 0, 2);

foreach ($recipients as $recipientId) {
    $result = $conn->query("SELECT phone FROM recipient WHERE recipient_id = " . intval($recipientId));
    $recipient = $result->fetch_assoc();
    $errorMessage = '';
    try {
        $client->messages->create(
            $recipient['phone'],
            ['from' => $twilio_number, 'body' => $message]
        );
        $successCount++;
        $status = 'success';
    } catch (Exception $e) {
        $status = 'failed';
        $errorMessage = $e->getMessage();
        $failureCount++;
    }
    $sql  = "insert into sms_logs (user_phone, sent_message, sent_by, status, error_message, user) values ('$recipient[phone]', '$message', $_SESSION[admin_id], '$status', '$errorMessage', 'recipient')";
    $conn->query($sql);
}

// Log the message
$recipientsCount = count($recipients);
$sql  = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'SMS_SENT', 'recipient', 0, 'Sent SMS to $recipientsCount recipients. Using this message: $message')";
$conn->query($sql);

echo json_encode(['message' => "SMS sent to $successCount numbers; failed for $failureCount."]);
?>
