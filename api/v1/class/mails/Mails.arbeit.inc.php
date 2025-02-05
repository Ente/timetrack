<?php

namespace Arbeitszeit;
use Arbeitszeit\Mails\MailsProviderInterface;
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
            throw new Exceptions("Mail-Template '$templateName' nicht gefunden.");
        }

        [$filePath, $className] = self::$templates[$templateName];
        require $filePath;

        if (strpos($className, "\\") === false) {
            $className = "Arbeitszeit\\Mails\\Templates\\$className";
        }
        $template = new $className();


        $mailContent = $template->render($data);

        return self::$provider->send($mailContent);
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
            foreach(self::$defaultTemplates as $template){
                $filePath = __DIR__ . "/templates/$template.mails.arbeit.inc.php";
                self::registerTemplate($template, $filePath, $template);
            }
        }
    }
}