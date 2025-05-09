<?php

namespace Arbeitszeit;
use Arbeitszeit\Mails\MailsProviderInterface;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Mails\MailTemplateData;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Arbeitszeit\Events\EventDispatcherService;
use Arbeitszeit\Events\SentMailEvent;

class Mails
{

    private static $provider;
    private static $templates = [];
    private static $configFile = __DIR__ . '/mails_registry.php';
    private static $defaultTemplates = [
        "DeleteUserTemplate",
        "NewUserTemplate",
        "PasswordChangedTemplate",
        "PasswordResetTemplate",
        "PasswordSendTemplate",
        "SicknessApprovedTemplate",
        "SicknessPendingTemplate",
        "SicknessRejectedTemplate",
        "VacationApprovedTemplate",
        "VacationPendingTemplate",
        "VacationRejectedTemplate",
        "WorktimeUncompliantTemplate"

    ];

    public static function init(MailsProviderInterface $provider)
    {
        self::$provider = $provider;
        self::loadTemplates();
    }

    public static function sendMail(string $templateName, array $data)
{
    if (!isset(self::$templates[$templateName])) {
        throw new \Exception("Mail-Template '$templateName' not found.");
    }

    [$filePath, $className] = self::$templates[$templateName];
    require_once $filePath;

    if (strpos($className, "\\") === false) {
        $className = "Arbeitszeit\\Mails\\Templates\\$className";
    }

    $template = new $className();

    if (!$template instanceof Mails\MailsTemplateInterface) {
        throw new \Exception("Mail-Template '$templateName' not implemented into MailsTemplateInterface.");
    }

    $mailContent = $template->render($data);

    $ini = Arbeitszeit::get_app_ini()["smtp"];
    if(!$ini["smtp"]){
        Exceptions::error_rep("SMTP disabled, not sending mail.");
        return true;
    } else {
        EventDispatcherService::get()->dispatch(new SentMailEvent($mailContent->toArray()["to"], $mailContent->toArray()["subject"], $mailContent->toArray()["body"]), SentMailEvent::NAME);
        return self::$provider->send($mailContent->toArray());
    }
}


    public static function registerTemplate(string $name, string $filePath, string $className)
    {
        self::$templates[$name] = [$filePath, $className];
        self::saveTemplates();
    }

        private static function saveTemplates()
    {
        file_put_contents(self::$configFile, '<?php return ' . var_export(self::$templates, true) . ';');
    }

    private static function loadTemplates()
{
    if (file_exists(self::$configFile)) {
        self::$templates = include self::$configFile;
    } else {
        foreach (self::$defaultTemplates as $template) {
            $filePath = __DIR__ . "/templates/$template.mails.arbeit.inc.php";
            self::registerTemplate($template, $filePath, $template);
        }
        self::saveTemplates();
    }

    // Scanne das Verzeichnis und registriere nur neue Templates
    self::scanAndRegisterTemplates();
}

private static function scanAndRegisterTemplates()
{
    $templateDir = __DIR__ . '/templates/';
    if (!is_dir($templateDir)) {
        return;
    }

    foreach (glob($templateDir . '*.mails.arbeit.inc.php') as $filePath) {
        $fileName = basename($filePath, '.mails.arbeit.inc.php');

        if (!isset(self::$templates[$fileName])) {
            self::registerTemplate($fileName, $filePath, $fileName);
        }
    }
}
}