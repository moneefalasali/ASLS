<?php
$opts = ['http' => ['method' => 'GET', 'header' => "Accept: application/json\r\n", 'ignore_errors' => true]];
$ctx = stream_context_create($opts);
$url = 'http://127.0.0.1:8000/api/v1/signs?category=words&language=ar&per_page=50';
$res = @file_get_contents($url, false, $ctx);
if ($res === false) {
    $err = error_get_last();
    echo "ERROR: " . ($err['message'] ?? 'unknown') . PHP_EOL;
    if (isset($http_response_header)) {
        echo "HEADERS:\n" . implode("\n", $http_response_header) . "\n";
    }
    exit(1);
}

// Print headers and body
if (isset($http_response_header)) {
    echo "HEADERS:\n" . implode("\n", $http_response_header) . "\n\n";
}
echo $res;
