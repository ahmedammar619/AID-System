<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'php/db.php';
$sql = "truncate table test2";
$conn->query($sql);


// Function to clean phone numbers (removes non-numeric characters and trims leading '1')
function cleanPhoneNumber($phone) {
    // Remove everything except digits
    $cleaned_phone = preg_replace('/\D/', '', $phone);

    // Check if it starts with '1', if so, remove it
    if (substr($cleaned_phone, 0, 1) === '1') {
        $cleaned_phone = substr($cleaned_phone, 1);
    }
    //check if it wasn't a us phone
    if(strlen($cleaned_phone) != 10){
        // echo $phone. '<br>';
        // echo $cleaned_phone. '<hr>';
    }

    return $cleaned_phone;
}

// Function to format dates to MySQL format (YYYY-MM-DD) and remove empty quotes
function formatDate($date) {
    $timestamp = strtotime($date);
    if ($timestamp) {
        return date('Y-m-d H:i:s', $timestamp); // Format to 'YYYY-MM-DD HH:MM:SS'
    } else {
        //return now date
        return date('Y-m-d H:i:s');
    }
}

// Function to process income_per column
function processCarSize($car) {
    $car = strtolower($car); 
    if (str_contains($car, '6 boxes')) {
        return '6'; 
    } elseif (str_contains($car, '8 boxes')) {
        return '8'; 
    } elseif(str_contains($car, '12 boxes')) {
        return '12'; 
    } elseif(str_contains($car, '18 boxes')) {
        return '18'; 
    } elseif(str_contains($car, '48 boxes')) {
        return '48'; 
    }
    else {
        return 'none';
    }
}

function processHelpsWith($helps_with) {
    $helps_with = strtolower($helps_with);
    if (str_contains($helps_with, 'and')) {
        return 'Both';
    } elseif (str_contains($helps_with, 'delivery')) {
        return 'Delivery';
    } elseif (str_contains($helps_with, 'pack')) {
        return 'Packing';
    } else {
        return 'none';
    }
}

function processPreference($preference) {
    $preference = strtolower($preference);
    if (str_contains($preference, 'first saturday')) {
        return 'Committed every month';
    } elseif (str_contains($preference, 'cannot')) {
        return 'Reminder every month';
    }elseif (str_contains($preference, 'only')) {
        return 'Committed one time';
    } 
    else {
        return 'none';
    }
}


$count = 0;


// Open the original CSV file
$inputFile = 'test_vol.csv';
$outputFile = 'test_cleaned2.csv';
$emailNumbers = array();





if (($inputHandle = fopen($inputFile, 'r')) !== FALSE) {
    // Open a new CSV file to write the cleaned data
    $outputHandle = fopen($outputFile, 'w');

    // Read the first line of CSV which contains the headers
    $headers = fgetcsv($inputHandle);

    // Write the headers to the new file
    fputcsv($outputHandle, $headers);

    // Iterate through the rows of the original CSV file

    while(($data = fgetcsv($inputHandle, 1000, ',')) !== FALSE) {
        // Assuming column positions (adjust as needed)
        $reg_date = array_search('reg_date', $headers);
        $full_name = array_search('full_name', $headers);
        $phone = array_search('phone', $headers);
        $email = array_search('email', $headers);
        $comments = array_search('comments', $headers);
        $preference = array_search('preference', $headers);
        $helps_with = array_search('helps_with', $headers);
        $car_size = array_search('car_size', $headers);
        
        $data[$reg_date] = formatDate($data[$reg_date]); // Ensure empty or NULL is handled cleanly

        $data[$phone] = cleanPhoneNumber($data[$phone]);

        $data[$preference] = processPreference($data[$preference]);

        $data[$helps_with] = processHelpsWith($data[$helps_with]);

        $data[$car_size] = processCarSize($data[$car_size]);
        
        // echo $data[$num_seniors]. "<br>";
        echo $data[$phone]. "<br>";
        $sql = "insert into test2 (full_name, phone, email, comment, preference, helps_with, car_size, reg_date) values ('$data[$full_name]', '$data[$phone]', '$data[$email]', '$data[$comments]', '$data[$preference]', '$data[$helps_with]', '$data[$car_size]', '$data[$reg_date]')";
        $conn->query($sql);
        // echo $conn->error;
        
        // count phone numbers none unique
        // $phone = $data[$phone];
        // if (in_array($phone, $phoneNumbers)) {
        //     // echo $phone . "<br>";
        //     $count++;
        // } else {
        //     $phoneNumbers[] = $phone;
        // }

        // $email = $data[$email];
        // if (in_array($email, $emailNumbers)) {
        //     echo $email . "<br>";
        //     $count++;
        // } else {
        //     $emailNumbers[] = $email;
        // }

        // Write the cleaned row into the new CSV file
        fputcsv($outputHandle, $data);
    }

    // Close the file handles
    fclose($inputHandle);
    fclose($outputHandle);
    // echo $count. "<br>";
    echo "CSV has been cleaned and saved to $outputFile.";
} else {
    echo "Error opening the CSV file.";
}
?>
