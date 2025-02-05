# Mails.php

## Developer: How to send mails:

Currently you do have to make sure that your custom provider is loaded before you can send mails. Simply require your custom provider and follow belows code example to send a mail. In the future you should be able to register a provider aswell and autoload it.

```php

require_once "path/to/customProvider.php";
$provider = new CustomProvider();
$arbeit->mails()->init($provider);

# Register email template | registerTemplate(string $templateName, string $classFilePath, string $className)
$arbeit->mails()->registerTemplate("TemplateName", __DIR__ . "path/to/TemplateName.php", "TemplateName");

# Send mail | sendMail(string $templateName, array $data) - $data is passed to the render function of the MailTemplate
$arbeit->mails()->sendMail("TemplateName", $someDataArray);

```

## Implementing the MailTemplateInterface

You can implement the MailTemplateInterface to create a new mail template to send to users. The interface has one method to be implemented:

```php
interface MailsTemplateInterface {
    public function render(array $data);
}

```

You can implement your own MailTemplate like this:

```php
<?php
namespace Arbeitszeit\Mails\Templates;
use Arbeitszeit\Mails\MailTemplateInterface;
use Arbeitszeit\Arbeitszeit;

class TemplateName implements MailTemplateInterface {
    public function render(array $data) { 
        // do stuff and compute plain text or HTML message

        

        // you MUST return array with keys: subject, body and username
        return [
            "subject" => "Subject of the mail",
            "body" => "Body of the mail",
            "username" => "Username"
        ]
    }
}


```

## Implementing the MailProviderInterface

The MailProviderInterface is used to configure your mail settings. The interface has one method to be implemented:

```php

interface MailProviderInterface {
    public function sendMail($mailData);
}

```

You can implement your own MailProvider like this:

```php

<?php
namespace Arbeitszeit\Mails\Provider;
class customProvider implements \Arbeitszeit\Mails\MailsProviderInterface {
    public function send(array $mailData) {
        $to = $mailData['to'];
        $subject = $mailData['subject'];
        $message = $mailData['body'];
        $headers = $mailData['headers'];
        return mail($to, $subject, $message, $headers);
    }
}


```
