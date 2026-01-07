<?php
require dirname(__FILE__, 5) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Telemetry;

$arbeit = new Arbeitszeit;
$telemetry = new Telemetry;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($telemetry->isTelemetryEnabled()){
        echo "Sending telemetry data...";
        $telemetry->getAndSendTelemetryData();
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("telemetry_sent"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("telemetry_disabled"));
    }
} else {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
?>