# TimeTrack - small enterprise time recording

TimeTrack aims to be a easy-to-use time recording software for small enterprises.
It's a fork from TimeTrack Oval, v6.2 (license-based model, within cloud & more features)

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

That's not even all of it, you can also generate timesheets (PDF) to export, user creation menu, an "easymode" to make it even easier to track your time and a mobile-friendly UI.
Additional functionality can be unlocked with TimeTrack Oval

## Installation

### Requirements

- at least PHP 8.0 (intl, pdo_mysql, curl, fileinfo, ldap, sockets extension)
- Apache2.4 with enabled htaccess, headers mod
- composer (to install dependencies; phpmailer: for sending emails via smtp, parsedown: markdown parser for the `CHANGELOG.md`, simple-router: does the API routing)

This software has been tested on Debian 11/12, XAMPP, PHP internal server (e.g. `php -S 0.0.0.0:80`).

### Install dependencies and TimeTrack

Simply install the software by following these steps:

- Install php and requirements: `apt update && apt install php8.0 php8.0-curl php8.0-mysqli apache2 mariadb-server -y` and enable the apache rewrite mod `a2enmod rewrite && service apache2 restart`
- Install requirements for composer `cd /path/to/timetrack && composer install`
- Create a new database, e.g. with the name `ab` and create a dedicated user, e.g. `timetool`: `CREATE DATABASE ab;` and `CREATE USER 'timetool'@'localhost' IDENTIFIED BY 'yourpassword';` and `GRANT ALL PRIVILEGES ON ab.* TO 'timetool'@'localhost';` don't forget to `FLUSH PRIVILEGES;`!
- Import the `setup/sql.sql` into your database, e.g. `mysql -u timetool -p ab < /full/path/to/sql.sql`
- To create your first user, run the `setup/usercreate.php` file, e.g. `php ./usercreate.php admin yourpassword email@admin.com` - `usercreate.php [USERNAME] [PASSWORD] [EMAIL]`
- Run the statement printed by the `usercreate.php` inside your database.
- Please run the `run-patch.sh` file located in the `setup` folder to apply a patch regarding LDAP authentication. If you do not want to use LDAP you can ignore this step.

### Configure app.ini/app.json

In step 2, you need to configure the `app.ini.sample`/`app.json.sample` within the `api/v1/inc` folder:

- `app_name`: The name of your application, e.g. `ACME Inc. TimeRecording`
- `base_url`: The Base URL (can also be an IP) of your application, without ending trailing slash and the protocol, e.g. `acme.inc` or `10.10.10.2` (URLs will be built with the http:// protocol, we recommend adding a redirect to https:// if you use an certificate.)
- `support_email`: An email displayed to users in case of help, e.g. `support@acme.inc`
- `debug`: (deprecated)
- `auto_update`: (not yet implemented)
- `db_*`: Set the connection details for your mysql instance
- `app`: If set to true, users will be able to use the TimeTrack mobile application

#### **SMTP section**

- `host`: FQDN of your mail server
- `username`: Username for the mailbox you want to send emails from
- `password`: Self explaining
- `port`: Specify a custom port or change the port if you do not want to use encryption
- `usessl`: Specify if you want to use STARTTLS (false) after initial communication or use SSL (true)

If you plan to use this system with a Gmail-Account, please be aware that you are not able to use your usual password. You would have to create a seperate `App Password`, you should note down.
You can do this following this link: <https://myaccount.google.com/u/0/apppasswords> or by navigating from <https://accounts.google.com> to `Security` > `2-Factor Authentication` > `App Passwords`. If you do not see this option on screen, use the link.

#### **Plugins** (Read more at `/api/v1/classes/plugins/docs`)

- `plugins`: Specify if you want to enable or disable plugins (default: true)
- `path`: Plugins path (default: `/api/v1/classes/plugins/plugins`)
- `data`: Data path for plugins, relative from the `path` variable (default: `data`)
- `testing`: Specify if the testing mode is enabled or not. If enabled, plugins which are not bundled within an phar archive are working aswell (e.g. just the source code within the plugins folder).

#### **LDAP**

LDAP authentication works with OpenLDAP and Active Directory.

- `ldap`: Specify if you want to enable (true) or disable (false) LDAP functionality (default: false)
- `ldap_user`: Serviceaccount to be used by timetrack (e.g. `sa-timetrack`)
- `ldap_password`: Base64 encoded LDAP user password
- `ldap_host`: FQDN of your LDAP server (e.g. `dc.example.local`)
- `ldap_ip`: IP address of your LDAP server (e.g. `1.1.1.1`)
- `ldap_domain`: The domain your LDAP server controls (e.g. `example.local`)
- `ldap_basedn`: Base DN for your domain (e.g. `dc=example,dc=local`)
- `ldap_group`: Group membership required by LDAP users to be able to authenticate
- `saf`: Specify if you only have one LDAP server (true) or another one as fallback (false)
- `saf_*`: If `saf` is set to `false`, please specify the corresponding values to the `saf_*` configuration
- `create_user`: If set to `true` it creates an user account automatically if the desired account is authenticated and within specified group. If set to `false` login simply fails, even if authenticated

If done correctly, you should now be able to access the application via http://BASE_URL/ - redirects to http://BASE_URL/suite/

**Please delete the whole `/setup/` folder after installation**

After configuring, please rename the `app.ini.sample`/`app.json.sample` to `app.ini`/`app.json` (`mv app.ini.sample app.ini`)

## Maintenance Mode

To enable the maintenance mode, simply rename the `api/inc/.MAINTENANCE` to `MAINTENANCE` (without the dot) to enable the functionality. No one will be able to access the application, aswell as administrators.
Disabling is done by renaming the file again.

## Permissions

TimeTrack only differenciates between two user groups:

- `Admin`: This group allows to change application settings, add notifications entries, manage users, manage worktime, sickness and vacation reports
- `User`: If in this group, you only have access to the elemental functions, like viewing notifications entries, add worktime/vacation/sickness

## Logging

Logs can be found inside the `./data/logs` path, they are named in the following scheme: log-{YEAR}-{MONTH}-{DAY}.log. Log files created do not get deleted automatically.
Another useful source, while expieriencing errors is the `/var/log/apache2/errors.log` file, containing the errors created by PHP.

## Language

TimeTrack supports German and English. Users currently can't actively switch between any of them, instead TimeTrack uses the locale provided by the browser.

## LDAP

TimeTrack allows you to use your existing LDAP server to authenticate your users against or create users automatically.
We use the users Username and the "Email" field from your LDAP to create the user within TimeTrack. A special setting `create_user` allows already existing users within the AD to register their account on their own.
Also at any time you can login with locally created accounts. However, if the LDAP user but not the local user account exists, login will be denied.
Already existing local accounts will get their authentication overwritten if an LDAP user is created with the same username afterwards.

In order to create accounts automatically if `create_user` is `true` make sure to set the user's email address! Otherwise login fails.

At the moment you have to create a user on your own locally and then let the user login with their LDAP credentials. The credentials you have entered will become usable if you disable LDAP or rename the account on your LDAP server.
Please run `run-patch.sh` within the `setup` folder to get LDAP working with php >8.0

## Export

The `ExportModule` allows you to export your data in any format as long as you have a `ExportModule` defined for it.
TimeTrack ships the `PDFExportModule` which allows you to export your data in PDF format through your browser.

You can define your own `ExporModules` by creating a new class in `api/v1/class/exports/modules/MyExportExportModule/MyExportExportModule.em.arbeit.inc.php` and implementing the `ExportModuleInterface` interface found in `api/v1/class/exports/modules/ExportModuleInterface.em.arbeit.inc.php`.

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

As there is currently no Export Area in the UI you have to create the GUI elements on your own.
You can specify your own CSS file within the `app.ini` `[exports][pdf][css]` setting (full path) - the default is `api/v1/class/exports/modules/PDFExportModule/css/index.css`

## QR codes

You can use the plugin `QRClock` to generate QR codes for yourself to either clock in or out. The QR code is generated can be used for later use, e.g. print it out.
Currently you do have to login before you can use the QR code. This will be reworked to bypass current authentication flow as there is a token embedded in the QR code. Therefore you should be careful with the QR code.

To use this feature, please download and place the `phpqrcode` folder into the `api/v1/class/plugins/plugins/qrclock/src` folder. You can download the `phpqrcode` library from <https://sourceforge.net/projects/phpqrcode/>.
When you have done this, you just have to enable the plugin by setting `enabled` within the `plugin.yml` to `true`.

The link to `phpqrcode` also contains a wiki if you want to modify the plugin.

## CodeClock Plugin

This plugin allows you to clock in or out using a PIN to authenticate. The plugin is disabled by default and must be enabled in the `plugin.yml`.
You can access the plugin by navigating to `Plugins` -> `[codeclock] View PIN`. Admins can reset PINs through the `Plugins` -> `[codeclock] Admin View` page. You must have once accessed the plugin to let it generate the PINs.

To login with the PIN navigate to http://BASE_URL/api/v1/toil/code and enter your PIN.

## Updates

TimeTrack has to be updated in two ways: database and application.

### Application

If downloaded from GitHub you can simply pull the latest release e.g. `git pull`
If downloaded any other way, just make sure to copy and paste the new files into TimeTrack's root directory.

### Database

You can update the database by downloading the `setup/upgrade.php` file into your local `setup` directory.
From here on just edit the `$missingUpdate` variable to the desired version as specified.

Please be aware that you are not able to skip an database update. You have to update one by one, e.g. from 1 -> 2, 2 -> 3, ...
