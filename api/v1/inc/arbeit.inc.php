<?php
/**
 * Script lädt Klassen
 */

if(file_exists(__DIR__ . "/MAINTENANCE") == true){
    header("Location: /errors/503.html");
    die();
}

require_once dirname(__DIR__, 1) . "/class/arbeitszeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/db/db.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/notifications/notifications.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/i18n/i18n.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/benutzer/benutzer.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/auth/auth.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/mode/mode.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/notifications/plugins/autodelete.notifications.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/exceptions/exceptions.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/vacation/vacation.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/sickness/sickness.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";

require_once dirname(__DIR__, 1) . "/class/exports/ExportModule.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/exports/modules/ExportModuleInterface.em.arbeit.inc.php";

require_once dirname(__DIR__, 1) . "/toil/toil.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/toil/Routes.toil.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/toil/Controller.toil.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/toil/resources/ep.toil.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/toil/Permissions.routes.toil.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/toil/CustomRoutes.routes.toil.arbeit.inc.php";

require_once dirname(__DIR__, 1) . "/class/plugins/Hooks.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/auth/plugins/ldap/ldap.auth.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/class/updates/updates.arbeit.inc.php";

require_once dirname(__DIR__ . 1) . "/class/mails/Mails.arbeit.inc.php";
require_once dirname(__DIR__ . 1) . "/class/mails/MailTemplateData.mails.arbeit.inc.php";
require_once dirname(__DIR__ . 1) . "/class/mails/interfaces/MailsProviderInterface.mails.arbeit.inc.php";
require_once dirname(__DIR__ . 1) . "/class/mails/interfaces/MailsTemplateInterface.mails.arbeit.inc.php";

require_once dirname(__DIR__ . 1) . "/class/mails/provider/PHPMailerMailsProvider.mails.arbeit.inc.php";
require_once dirname(__DIR__ . 1) . "/class/mails/provider/DefaultMailsProvider.mails.arbeit.inc.php";
use Arbeitszeit\Hooks;
Hooks::initialize();

use Arbeitszeit\Updates;
$updates = new Updates;
$updates->compare_scheme();
?>