<?php
$url = 'http://127.0.0.1:8000/storage/signs/en_a.png';
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: PHP-script\r\n"
    ]
];
$context = stream_context_create($opts);
$headers = @get_headers($url, 1, $context);
if ($headers === false) {
    echo "Request failed or server not reachable\n";
    exit(1);
}
print_r($headers);
