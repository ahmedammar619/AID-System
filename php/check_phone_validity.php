<?php
include '../php/db.php';
// Get the phone number and type (recipient/volunteer) from the GET request
$phone = $_GET['phone'] ?? '';
$type = $_GET['type'] ?? '';

// Validate the type
if (!in_array($type, ['recipient', 'volunteer'])) {
    echo json_encode(['error' => 'Invalid type']);
    exit;
}
// Prepare the SQL statement
$stmt = $conn->prepare("SELECT * FROM $type WHERE phone = ?");
$stmt->bind_param("s", $phone);

// Execute the query
$stmt->execute();
$stmt->store_result();

// Check if the phone number exists
if ($stmt->num_rows > 0) {
    echo json_encode(['registered' => true]);
} else {
    echo json_encode(['registered' => false]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>