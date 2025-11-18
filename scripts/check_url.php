<?php
$url = $argv[1] ?? null;
if (!$url) { echo "Usage: php check_url.php <url>\n"; exit(1); }
$headers = @get_headers($url, 1);
if ($headers === false) { echo "Request failed or server not reachable\n"; exit(1); }
print_r($headers);
