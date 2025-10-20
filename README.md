# TimeTrack - small enterprise time recording

TimeTrack aims to be an easy-to-use time recording software for small enterprises.

## Features

- Time recording for your employees (as well as Vacation and Sickness reporting)
- Email notifications
- Platform-wide notifications
- customizable API
- Logging
- EN/DE/NL language support
- Maintenance mode
- Easy and fast installation
- LDAP Authentication
- Supporting NFC Login
- Plugin Support
- Exporting to PDF/CSV

## Installation

### Requirements

- PHP 8.2 (`curl|gd|gmp|intl|mbstring|mysqli|openssl|xsl|gettext|dom|ldap`) - tested with PHP 8.2.26
- composer (to install dependencies; phpmailer: for sending emails via smtp, parsedown: markdown parser for the `CHANGELOG.md`, simple-router: does the API routing, yaml: for reading plugin yaml files, ldaptools: for LDAP authentication, dompdf: for PDF generation, phinx: for database migrations, event-dispatcher: for handling events, contracts: for defining events and interfaces)
- Apache2.4 with enabled rewrite mod (optional)

This software has been tested on Debian 11/12, XAMPP, PHP internal server (e.g. `php -S 0.0.0.0:80`).

### Install dependencies and TimeTrack

Simply install the software by following these steps:

- Install php and requirements: `sudo apt update && sudo apt install php8.2 php8.2-curl php8.2-gd php8.2-gmp php8.2-intl php8.2-mbstring php8.2-mysqli php8.2-pgsql php8.2-xsl php8.2-gettext php8.2-dom php8.2-ldap composer git mariadb-server apache2 -y` and enable the apache rewrite mod `a2enmod rewrite && service apache2 restart`. If you do not want to use apache2 you can skip this step.
- Git clone timetrack to e.g. `/var/www`: `cd /var/www && git clone https://github.com/Ente/timetrack.git && cd timetrack`
- Install requirements for composer `composer install`
- Create a new database, e.g. with the name `ab` and create a dedicated user, login (`mysql -u root -p`) then e.g. `timetool`: `CREATE DATABASE ab;` and `CREATE USER 'timetool'@'localhost' IDENTIFIED BY 'yourpassword';` and `GRANT ALL PRIVILEGES ON ab.* TO 'timetool'@'localhost';` don't forget to `FLUSH PRIVILEGES;`!
- Configure `app.json` (see below - required changes: `base_url`, `db_user`, `db_password`, `smtp` section and any other if your installation is different) then `mv api/v1/inc/app.json.sample app.json && cd /var/www/timetrack`
- Run DB migrations: `vendor/bin/phinx migrate`
- Start webserver e.g. `service apache2 stop && php -S 0.0.0.0:80` or using apache2 (then you have to configure the `sites-available` conf yourself)
- You can then access TimeTrack in your browser at `http://localhost`, default login is `admin` with password `admin`. Create yourself a new admin account, login and delete the default account afterwards.

To save log files, please create the subfolder `data/logs` and make it writeable to the web server (e.g. `chown www-data:www-data data/logs && chmod 775 data/logs`).
Please also make sure that the `/data` directory is writable by the webserver, aswell as the plugins directory (default: `api/v1/class/plugins/plugins`).

### Configure app.json

In step 2, you need to configure the `app.json.sample` within the `api/v1/inc` folder:

- `app_name`: The name of your application, e.g. `ACME Inc. TimeRecording`
- `base_url`: The Base URL (can also be an IP) of your application, without ending trailing slash and the protocol, e.g. `acme.inc` or `10.10.10.2` (URLs will be built with the http:// protocol, we recommend adding a redirect to https:// if you use an certificate.)
- `support_email`: An email displayed to users in case of help, e.g. `support@acme.inc`
- `debug`: (deprecated, but may still unlock certain functionality)
- `auto_update`: (not yet implemented)
- `db_*`: Set the connection details for your mysql instance
- `app`: If set to true, users will be able to use the TimeTrack mobile application
- `timezone`: Set the timezone of your application, e.g. `Europe/Berlin` or `America/New_York` (default: `UTC`)
- `force_theme`: Force a theme for all users, this disables the feature allowing users to set their own theme.
- `theme_file`: If `force_theme` is true, the specified theme is used (default: `/assets/css/v8.css`)

#### **SMTP section**

- `smtp`: Set to `true` to enable SMTP functionality (default: false)
- `host`: FQDN of your mail server
- `username`: Username for the mailbox you want to send emails from
- `password`: Self explaining
- `port`: Specify a custom port or change the port if you do not want to use encryption
- `usessl`: Specify if you want to use STARTTLS (false) after initial communication or use SSL (true)

If you plan to use this system with a Gmail-Account, please be aware that you are not able to use your usual password. You would have to create a seperete `App Password`, you should note down.
You can do this following this link: <https://myaccount.google.com/u/0/apppasswords> or by navigating from <https://accounts.google.com> to `Security` > `2-Factor Authentication` > `App Passwords`. If you do not see this option on screen, use the link.

#### **Plugins** (Read more at `/api/v1/classes/plugins/docs`)

- `plugins`: Specify if you want to enable or disable plugins (default: true)
- `path`: Plugins path (default: `/api/v1/classes/plugins/plugins`)
- `data`: Data path for plugins, relative from the `path` variable (default: `data`)
- `testing`: Specify if the testing mode is enabled or not. If enabled, plugins which are not bundled within an phar archive are working as well (e.g. just the source code within the plugins folder).

#### **LDAP**

LDAP authentication works with OpenLDAP and Active Directory.

- `ldap`: Specify if you want to enable (true) or disable (false) LDAP functionality (default: false)
- `ldap_user`: Serviceaccount to be used by timetrack (e.g. `sa-timetrack`)
- `ldap_password`: Base64 encoded LDAP user password
- `ldap_host`: FQDN of your LDAP server (e.g. `dc.example.local`)
- `ldap_ip`: IP address of your LDAP server (e.g. `1.1.1.1`)
- `ldap_domain`: The domain your LDAP server controls (e.g. `example.local`)
- `ldap_basedn`: Base DN for your domain (e.g. `dc=example,dc=local`)
- `ldap_group`: Group membership required by LDAP users to be able to authenticate (e.g. `Domain Users`, (new group) `TimeTrack Users`)
- `saf`: Specify if you only have one LDAP server (true) or another one as fallback (false)
- `saf_*`: If `saf` is set to `false`, please specify the corresponding values to the `saf_*` configuration
- `create_user`: If set to `true` it creates an user account automatically if the desired account is authenticated and within specified group. If set to `false` login simply fails, even if authenticated.

#### **Export**

##### **PDF**

- `css`: Full path to the CSS file used for the PDF export (default: `api/v1/class/exports/modules/PDFExportModule/css/index.css`) - **optional value**

If done correctly, you should now be able to access the application via http://BASE_URL/ - redirects to http://BASE_URL/suite/

**Please delete the whole `/setup/` folder after installation**

After configuring, please rename the `app.json.sample` to `app.json` (`mv app.json.sample app.json`)

## Maintenance Mode

To enable the maintenance mode, simply rename the `api/v1/inc/.MAINTENANCE` to `MAINTENANCE` (without the dot) to enable the functionality. No one will be able to access the application, as well as administrators.
Disabling is done by renaming the file again.

## Permissions

TimeTrack only differenciates between two user groups:

- `Admin`: This group allows to change application settings, add notifications entries, manage users, manage worktime, sickness and vacation reports
- `User`: If in this group, you only have access to the elemental functions, like viewing notifications entries, add worktime/vacation/sickness

## Logging

Logs can be found inside the `./data/logs` path, they are named in the following scheme: log-{YEAR}-{MONTH}-{DAY}.log. Log files created do not get deleted automatically.
Another useful source, while expieriencing errors is the `/var/log/apache2/errors.log` file, containing the errors created by PHP.

## Language

TimeTrack supports German, English and Dutch. Users currently can't actively switch between any of them, instead TimeTrack uses the locale provided by the browser.

## LDAP

TimeTrack allows you to use your existing LDAP server to authenticate your users against or create users automatically.
We use the users Username and the "Email" field from your LDAP to create the user within TimeTrack. A special setting `create_user` allows already existing users within the AD to register their account on their own.
Also at any time you can login with locally created accounts. However, if the LDAP user but not the local user account exists, login will be denied.
Already existing local accounts will get their authentication overwritten if an LDAP user is created with the same username afterwards.

In order to create accounts automatically if `create_user` is `true` make sure to set the user's email address! Otherwise login fails.

If above mentioned setting is set to `false` you have to create a user on your own locally and then let the user login with their LDAP credentials. The credentials you have entered will become usable if you disable LDAP or rename the account on your LDAP server.

## Export

The `ExportModule` allows you to export your data in any format as long as you have a `ExportModule` defined for it.
TimeTrack ships the `PDFExportModule` and `CSVExportModule` which allows you to export your data in PDF/CSV format through your browser/file.

You can define your own `ExportModules` by creating a new class in `api/v1/class/exports/modules/MyExportExportModule/MyExportExportModule.em.arbeit.inc.php` and implementing the `ExportModuleInterface` interface found in `api/v1/class/exports/modules/ExportModuleInterface.em.arbeit.inc.php`.

You can then use your new `MyExportExportModule` the following:

```php
<?php
require_once 'path/to/arbeitszeit.inc.php';
use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit();

$data = $arbeit->get_all_user_worktime("username");

$arbeit->exportModule()->export(["module" => "MyExportExportModule", "data" => $data]);
// OR
$arbeit->exportModule()->getExportModule("MyExportExportModule")->export($data);

```

All existing export modules can be accessed with the `ExportManager` Plugin.
You can specify your own CSS file within the `app.json` `exports -> pdf -> css` setting (full path) - the default is `api/v1/class/exports/modules/PDFExportModule/css/index.css`

## QR codes

You can use the plugin `QRClock` to generate QR codes for yourself to either clock in or out. The QR code generated can be saved for later use, e.g. print it out.
Currently you do have to login before you can use the QR code. This will be reworked to bypass current authentication flow as there is a token embedded in the QR code. Therefore you should be careful with the QR code.

To use this feature, please download and place the `phpqrcode` folder into the `api/v1/class/plugins/plugins/qrclock/src` folder. You can download the `phpqrcode` library from <https://sourceforge.net/projects/phpqrcode/>.
When you have done this, you just have to enable the plugin by setting `enabled` within the `plugin.yml` to `true`.

The link to `phpqrcode` also contains a wiki if you want to modify the plugin.

## CodeClock Plugin

This plugin allows you to clock in or out using a PIN to authenticate. The plugin is disabled by default and must be enabled in the `plugin.yml`.
You can access the plugin by navigating to `Plugins` -> `[codeclock] View PIN`. Admins can reset PINs through the `Plugins` -> `[codeclock] Admin View` page. You must have once accessed the plugin to let it generate the PINs.

To login with the PIN navigate to http://BASE_URL/api/v1/toil/code and enter your PIN.

## Projects

Administrators can now create Projects within the `Projects management` tab.
Users can access their projects within the `Projects` tab.

You can create items for their projects and map worktimes to it. The feature will be reworked in the future.

## Themes

Users can now select their own theme within the `Settings` page. It loads all available themes that reside within the `/assets/css` folder.
Administrators can enforce a theme globally by setting `force_theme` to `true`. If so, only the theme specified within `theme_file` is available.

To upload a new theme, simply place it into the `/assets/css` folder.

The theme the user selected is saved as a cookie, meaning it is only selected on the current device. On mobile or on another device, the user has to set the desired theme again.

## Updates

TimeTrack has to be updated in two ways: database and application.

### Application

If downloaded from GitHub you can simply pull the latest release e.g. `git pull`
If downloaded any other way, just make sure to copy and paste the new files into TimeTrack's root directory.
**Check the changelogs**: They usually tell you if you need to update composer dependencies (`composer update`) or if a database migration is required.

### Database

You can update the database by using `vendor/bin/phinx migrate` to migrate to latest release or `vendor/bin/phinx rollback` to rollback.

## Managed Hosting

If you don't want to worry about installation, updates and maintenance - let us do it for you.

With our managed TimeTrack hosting, you get:

* 🛡️ Fully GDPR-compliant hosting in Germany or Netherlands
* 🚀 Always up-to-date with the latest features and security patches
* 🔐 Secure HTTPS out-of-the-box
* ☁  Backups, monitoring and support included
* 🧩 Custom plugins, integration, branding

Want to move your team to the cloud? Check out our <a href="https://openducks.org/timetrack.php">website</a> for more information.

## License

The original project is licensed under the GPLv3 license - see the [LICENSE](LICENSE) file for details.
