<?php
/**
 * Script lädt Klassen
 */
#ini_set("display_errors", 1);
#ini_set("default_charset", "UTF-8");

if(file_exists(__DIR__ . "/MAINTENANCE") == true){
    header("Location: /errors/503.html");
} else {
    #echo "false";
}

require_once dirname(__DIR__, 1) . "/class/arbeitszeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/kalender/kalender.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/benutzer/benutzer.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/auth/auth.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/pdf/pdf.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/mailbox/mailbox.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/mode/mode.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/url_handling/url_handling.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/kalender/plugins/autodelete.kalender.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/exceptions/exceptions.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/vacation/vacation.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/sickness/sickness.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once dirname(__DIR__, 1) . "/toil/Controller.php";


?>