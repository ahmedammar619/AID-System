<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'php/db.php';
// $sql = "truncate table test";
// $conn->query($sql);


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
        return NULL; // Return NULL if date is invalid or empty
    }
}


function processIncome($income) {
    $income = preg_replace("/[^0-9.]/", "", $income);    
    return $income ? floatval($income): '0'; // Return cleaned income or NULL if empty
}

// Function to process income_per column
function processIncomePer($income_per) {
    $income_per = strtolower($income_per); // Convert to lowercase for comparison
    if (str_contains($income_per, 'month')) {
        return 'Per Month'; // If 'month' is found, return 'Per Month'
    } elseif (str_contains($income_per, 'no')) {
        return 'No Income'; // If 'no' is found, return 'No Income'
    } elseif(str_contains($income_per, 'week')) {
        return 'Per Week'; // If 'week' is found, return 'Per Week'
    } elseif(str_contains($income_per, 'year')) {
        return 'Per Year'; // If 'year' is found, return 'Per Year'
    }
    else {
        echo $income_per;

        return NULL; // Default if no matching condition
    }
}
$count = 0;
function processNationality($nationality) {
    $nationality = strtolower($nationality); // Convert to lowercase for comparison
    if(str_contains($nationality, 'native')) {
        return 'American Indian or Alaska Native'; // If 'american' is found, return 'American'
    } elseif(str_contains($nationality, 'asian')) {
        return 'Asian'; // If 'mexican' is found, return 'Mexican'
    } elseif(str_contains($nationality, 'african')) {
        return 'Black or African American'; // If 'canadian' is found, return 'Canadian'
    } elseif(str_contains($nationality, 'hispanic')) {
        return 'Hispanic or Latino'; // If 'hispanic' is found, return 'Hispanic'
    } elseif(str_contains($nationality, 'white')) {
        return 'White'; // If 'white' is found, return 'White'
    }elseif(str_contains($nationality, 'islander')) {
        return 'Native Hawaiian or Other Pacific Islander'; // If 'islander' is found, return 'Islander'
    }
    else {
        echo $nationality;
        return 'Other'; // If 'other' is found, return 'Other'
    }
}


// Open the original CSV file
$inputFile = 'test_fam.csv';
$outputFile = 'test_cleaned.csv';

$emailNumbers = array();

$phoneNumbers = array();



// function getCoordinates($address) {
//     // Prepare the URL with the Nominatim API endpoint
//     $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";

//     // Initialize a cURL session
//     $ch = curl_init();

//     // Set cURL options
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');

//     // Execute the cURL request and get the response
//     $response = curl_exec($ch);

//     // Check if the request was successful
//     if (curl_errno($ch)) {
//         echo 'Error: ' . curl_error($ch);
//     }

//     // Close the cURL session
//     curl_close($ch);

//     echo "Raw API Response: <pre>$response</pre>";

//     // Decode the JSON response
//     $data = json_decode($response, true);

//     // Print the decoded response for debugging
//     echo "Decoded Response: <pre>" . print_r($data, true) . "</pre>";
    

//     // Check if the response contains valid data
//     if (isset($data[0])) {
//         $latitude = $data[0]['lat'];
//         $longitude = $data[0]['lon'];
//         echo $latitude. ', ' . $longitude. '<br>';
//         echo 'YEHAAS <br>';
//         return [
//             'latitude' => $latitude,
//             'longitude' => $longitude
//         ];
//     } else {
//         echo 'no <br>';
//         return [
//             'latitude' => null,
//             'longitude' => null
//         ];
//     }
// }


function getCoordinates($address) {
    $apiKey = 'OPEN_CAGE_API_KEY'; // Replace with your OpenCage API key
    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($address) . "&key=" . $apiKey;

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the response contains valid data
    if (isset($data['results'][0]['geometry'])) {
    $latitude = $data['results'][0]['geometry']['lat'];
    $longitude = $data['results'][0]['geometry']['lng'];

        return [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
    } else {
        return [
            'latitude' => null,
            'longitude' => null
        ];
    }
}




if (($inputHandle = fopen($inputFile, 'r')) !== FALSE) {
    // Open a new CSV file to write the cleaned data
    $outputHandle = fopen($outputFile, 'w');

    // Read the first line of CSV which contains the headers
    $headers = fgetcsv($inputHandle);

    // Write the headers to the new file
    fputcsv($outputHandle, $headers);

    // Iterate through the rows of the original CSV file
    // $x = 0;
    // while($x!=2){
    //     $x++;
    // $data = fgetcsv($inputHandle, 1000, ',');
    while(($data = fgetcsv($inputHandle, 1000, ',')) !== FALSE) {
        // Assuming column positions (adjust as needed)
        $full_name = array_search('full_name', $headers);
        $phone = array_search('phone', $headers);
        $address = array_search('address', $headers);
        $city = array_search('city', $headers);
        $apt_num = array_search('apt_num', $headers);
        $zip_code = array_search('zip_code', $headers);
        $num_total = array_search('num_total', $headers);
        $num_children = array_search('num_children', $headers);
        $num_adults = array_search('num_adults', $headers);
        $num_seniors = array_search('num_seniors', $headers);
        $longitude = array_search('longitude', $headers);
        $latitude = array_search('latitude', $headers);

        $coords = getCoordinates($data[$address]. ', ' . $data[$city]);
        if($coords['latitude'] == null){
            $coords = getCoordinates($data[$address]);
            if($coords['latitude'] == null){
                $data[$latitude] = 0;
                $data[$longitude] = 2;
                echo $data[$address] . ', ' . $data[$city]. '<br>';
                echo 'ADDRESS FAUILT: '.$data[$phone]. '<hr>';
            }else{
                $data[$latitude] = $coords['latitude'];
                $data[$longitude] = $coords['longitude'];
                // echo $coords['latitude']. ', ' . $$coords['longitude']. '<br>';
            }
            echo ' CITY FAUILT: '.$data[$phone]. '<br>';
        }else{
            $data[$latitude] = $coords['latitude'];
            $data[$longitude] = $coords['longitude'];
            // echo $coords['latitude']. ', ' . $$coords['longitude']. '<br>';
        }
        $data[$phone] = cleanPhoneNumber($data[$phone]);
        
        $data[$num_seniors] = $data[$num_seniors] == '' ? 0 : $data[$num_seniors];
            
        $sql = "insert into recipient (full_name, phone, address, city, apt_num, zip_code, num_total, num_children, num_adults, num_seniors, longitude, latitude) values ('$data[$full_name]', '$data[$phone]', '$data[$address]', '$data[$city]', '$data[$apt_num]', '$data[$zip_code]', '$data[$num_total]', '$data[$num_children]', '$data[$num_adults]', '$data[$num_seniors]', '$data[$longitude]', '$data[$latitude]')";
        $conn->query($sql);
        // echo $conn->error;
        
        // count phone numbers none unique
        $phone = $data[$phone];
        if (in_array($phone, $phoneNumbers)) {
            echo $phone . "<br>";
            $count++;
        } else {
            $phoneNumbers[] = $phone;
        }

        // $email = $data[$email];
        // if (in_array($email, $emailNumbers)) {
        //     echo $email . "<br>";
        //     $count++;
        // } else {
        //     $emailNumbers[] = $email;
        // }

        // Write the cleaned row into the new CSV file
        // fputcsv($outputHandle, $data);
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
