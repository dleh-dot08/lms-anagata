<?php

$url = "http://127.0.0.1:8000/";
$totalRequests = 8; // We'll make 8 requests to ensure we hit the limit (5)
$successCount = 0;
$blockedCount = 0;

echo "Starting test...\n";
echo "Making $totalRequests requests to $url...\n";
echo "Expecting to be blocked after 5 requests...\n\n";

for ($i = 0; $i < $totalRequests; $i++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "Request " . ($i + 1) . ": ";
    
    if ($error) {
        echo "Error: $error\n";
    } else {
        if ($httpCode === 429) {
            $blockedCount++;
            echo "Blocked (429) - IP has been blocked!\n";
        } else {
            $successCount++;
            echo "Success ($httpCode)\n";
        }
    }
    
    // Add a small delay between requests (0.1 seconds)
    usleep(100000);
}

echo "\nTest completed!\n";
echo "Successful requests: $successCount\n";
echo "Blocked requests: $blockedCount\n";

if ($blockedCount === 0) {
    echo "\nIP blocking might not be working as expected. No requests were blocked.\n";
} else {
    echo "\nIP blocking is working! Requests were blocked after $successCount successful requests.\n";
} 