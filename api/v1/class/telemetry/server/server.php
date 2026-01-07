<?php
require_once dirname(__FILE__, 4) . "/inc/arbeit.inc.php";

use Arbeitszeit\Telemetry\Server\Server;

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if ($path === "/timetrack/submit") {
    $server = new Server();
    exit;
}

if ($path === "/") {
    echo "Telemetry Server Online";
    exit;
}

http_response_code(404);
echo "Not found.";
