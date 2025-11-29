<?php

$payload = json_encode(['text' => 'مرحبا', 'language' => 'ar']);
$opts = ['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => $payload]];
$ctx = stream_context_create($opts);
$res = file_get_contents('http://127.0.0.1:8000/api/convert-text', false, $ctx);
var_dump($res);

?>